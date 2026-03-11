# ============================================================
# Stage 1: Build
# ============================================================
FROM node:22-alpine AS builder

WORKDIR /app

# Install dependencies first (layer cache)
COPY package.json package-lock.json ./
RUN npm ci

# Copy source and build
COPY . .
RUN npm run build

# ============================================================
# Stage 2: Production image
# ============================================================
FROM node:22-alpine

# Create non-root user
RUN addgroup -S appgroup && adduser -S appuser -G appgroup

WORKDIR /app

# Copy only the Nitro server output
COPY --from=builder /app/.output .output/

# Switch to non-root user
USER appuser

# Cloud Run injects PORT env var (default 8080)
ENV PORT=8080
ENV HOST=0.0.0.0
ENV NODE_ENV=production
EXPOSE 8080

HEALTHCHECK --interval=30s --timeout=5s --retries=3 \
    CMD wget -qO- http://localhost:${PORT}/ || exit 1

CMD ["node", ".output/server/index.mjs"]
