<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * セッション永続化テスト
 *
 * ログイン後のセッション維持（リロード相当）やログアウト後のセッション無効化を検証する。
 * actingAs() を使わず、実際の POST /api/login 経由でセッションを確立し、
 * 後続リクエストでセッションが維持されることを確認する。
 *
 * Sanctum SPA 認証ではリクエストの Origin が stateful domains に含まれている必要がある。
 * テストでは Origin: http://localhost ヘッダーを付与して stateful リクエストを再現する。
 */
final class SessionPersistenceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'session-test@example.com',
            'password' => bcrypt('password123'),
        ]);
    }

    /**
     * Sanctum SPA 認証に必要な Origin ヘッダー付きで JSON リクエストを送信するヘルパー。
     */
    private function statefulPostJson(string $uri, array $data = []): \Illuminate\Testing\TestResponse
    {
        return $this->withHeaders(['Origin' => 'http://localhost'])
            ->postJson($uri, $data);
    }

    private function statefulGetJson(string $uri): \Illuminate\Testing\TestResponse
    {
        return $this->withHeaders(['Origin' => 'http://localhost'])
            ->getJson($uri);
    }

    // ========================================
    // ログイン後のセッション永続化
    // ========================================

    public function test_ログイン後にセッションが維持され認証済みリクエストが成功する(): void
    {
        // ログインリクエストを送信
        $loginResponse = $this->statefulPostJson('/api/login', [
            'email' => 'session-test@example.com',
            'password' => 'password123',
        ]);

        $loginResponse->assertOk()
            ->assertJsonPath('data.email', 'session-test@example.com');

        // ログインで確立されたセッションを使って GET /api/user を呼ぶ
        // Laravel のテストクライアントは同一テスト内でセッション状態を維持する
        $userResponse = $this->statefulGetJson('/api/user');

        $userResponse->assertOk()
            ->assertJsonStructure([
                'data' => ['id', 'name', 'email'],
            ])
            ->assertJsonPath('data.email', 'session-test@example.com')
            ->assertJsonPath('data.name', 'テストユーザー');
    }

    public function test_ログイン後に複数回のリクエストでセッションが維持される(): void
    {
        // ログイン
        $this->statefulPostJson('/api/login', [
            'email' => 'session-test@example.com',
            'password' => 'password123',
        ])->assertOk();

        // 1回目の認証確認
        $this->statefulGetJson('/api/user')
            ->assertOk()
            ->assertJsonPath('data.email', 'session-test@example.com');

        // 2回目の認証確認（セッションが複数リクエストを跨いで維持されること）
        $this->statefulGetJson('/api/user')
            ->assertOk()
            ->assertJsonPath('data.email', 'session-test@example.com');
    }

    public function test_ログインレスポンスにユーザー情報が正しく含まれる(): void
    {
        $response = $this->statefulPostJson('/api/login', [
            'email' => 'session-test@example.com',
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'data' => ['id', 'name', 'email'],
            ])
            ->assertJson([
                'data' => [
                    'id' => $this->user->id,
                    'name' => 'テストユーザー',
                    'email' => 'session-test@example.com',
                ],
            ]);
    }

    // ========================================
    // ログアウト後のセッション無効化
    // ========================================

    public function test_ログイン後にログアウトすると認証状態がクリアされる(): void
    {
        // ログイン
        $this->statefulPostJson('/api/login', [
            'email' => 'session-test@example.com',
            'password' => 'password123',
        ])->assertOk();

        // 認証状態を確認
        $this->statefulGetJson('/api/user')
            ->assertOk()
            ->assertJsonPath('data.email', 'session-test@example.com');

        // ログアウト
        $logoutResponse = $this->statefulPostJson('/api/logout');
        $logoutResponse->assertNoContent();

        // ログアウト後は web guard で認証されていないことを確認
        $this->assertFalse(
            \Illuminate\Support\Facades\Auth::guard('web')->check(),
            'ログアウト後は web guard で認証されていないこと'
        );

        // ログアウト後は Auth::user() が null であること
        $this->assertNull(
            \Illuminate\Support\Facades\Auth::guard('web')->user(),
            'ログアウト後は web guard の user が null であること'
        );
    }

    public function test_ログアウトは204を返しセッションを破棄する(): void
    {
        // ログイン
        $this->statefulPostJson('/api/login', [
            'email' => 'session-test@example.com',
            'password' => 'password123',
        ])->assertOk();

        // ログアウトが 204 No Content を返すこと
        $this->statefulPostJson('/api/logout')
            ->assertNoContent();
    }

    // ========================================
    // 未認証リクエスト
    // ========================================

    public function test_一度もログインしていない状態で_ge_t_api_userが401を返す(): void
    {
        $this->statefulGetJson('/api/user')
            ->assertUnauthorized();
    }

    public function test_未認証で_pos_t_api_logoutが401を返す(): void
    {
        $this->statefulPostJson('/api/logout')
            ->assertUnauthorized();
    }

    public function test_不正な認証情報でログイン失敗後にセッションが確立されない(): void
    {
        // 不正なパスワードでログイン試行
        $this->statefulPostJson('/api/login', [
            'email' => 'session-test@example.com',
            'password' => 'wrong-password',
        ])->assertUnprocessable();

        // セッションが確立されていないことを確認
        $this->statefulGetJson('/api/user')
            ->assertUnauthorized();
    }

    // ========================================
    // ユーザー切り替え（別テストメソッドで分離）
    // ========================================

    public function test_異なるユーザーでログインすると正しいユーザー情報が返る(): void
    {
        // 2人目のユーザーを作成
        User::factory()->create([
            'name' => '別のユーザー',
            'email' => 'other@example.com',
            'password' => bcrypt('password456'),
        ]);

        // 2人目でログイン（1人目はログインしない）
        $this->statefulPostJson('/api/login', [
            'email' => 'other@example.com',
            'password' => 'password456',
        ])->assertOk();

        // 2人目のユーザー情報が返ることを確認
        $this->statefulGetJson('/api/user')
            ->assertOk()
            ->assertJsonPath('data.email', 'other@example.com')
            ->assertJsonPath('data.name', '別のユーザー');
    }

    public function test_1人目でログイン後のセッションで1人目の情報が返る(): void
    {
        // 2人目も存在するが、1人目でログイン
        User::factory()->create([
            'name' => '別のユーザー',
            'email' => 'other@example.com',
            'password' => bcrypt('password456'),
        ]);

        $this->statefulPostJson('/api/login', [
            'email' => 'session-test@example.com',
            'password' => 'password123',
        ])->assertOk();

        // 1人目のユーザー情報が返る（2人目のデータが混ざらないこと）
        $this->statefulGetJson('/api/user')
            ->assertOk()
            ->assertJsonPath('data.email', 'session-test@example.com')
            ->assertJsonPath('data.name', 'テストユーザー');
    }
}
