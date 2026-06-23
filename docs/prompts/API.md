# WAKA PMS — API Layer (REST, GraphQL, Webhooks, Gateway)

**Stack:** REST (OpenAPI 3.1), GraphQL, WebSocket, JWT/OAuth2/OIDC/SAML, API Gateway
**Audience:** API/platform engineering team / AI coding agent designing the WAKA PMS API contract

---

## 1. Prompt

```
You are an API architect designing the public and internal API surface for WAKA PMS — the
contract layer that the web frontend, Flutter mobile apps, USSD gateway, and third-party
integrators all consume. This is distinct from backend service implementation: focus on
contract design, versioning, gateway behavior, and developer experience.

DESIGN PRINCIPLES (from the platform PRD)
- API-first design with versioned REST and GraphQL endpoints
- Tenant-aware security baked into every request (no implicit trust of client-provided tenant IDs)
- Standard protocols: REST, GraphQL, WebSocket
- Import/export via CSV, Excel, JSON; webhook events for external systems
- Integration with payment providers, messaging services, identity providers, and BI tools

SCOPE
1. Versioning Strategy
   - Define a versioning scheme (e.g., URI versioning /v1/, /v2/) with a deprecation policy,
     sunset headers, and backward-compatibility rules for breaking vs. additive changes.

2. REST API Surface — define resource-oriented endpoints and OpenAPI 3.1 specs for:
   - Auth (login, refresh, MFA challenge, logout, SSO callback)
   - Tenancy/Org admin (tenants, properties, buildings, floors, units, amenities)
   - Tenant/lease lifecycle (tenants, leases, lease rules/fees, billing cycles)
   - Payments (invoices, receipts, payment methods, reconciliation, refunds, arrears)
   - Wallet & finance (balances, transactions, holds, expenses)
   - Utility billing & service charges (meters, tariffs, bills)
   - Maintenance & vendor (requests, work orders, vendor assignment)
   - Visitor management (registration, badges, check-in/out)
   - Documents (upload, metadata, versioning, signed URLs)
   - Notifications (preferences, templates, send status)
   - Reporting (report definitions, scheduled exports, dashboard data)
   - Audit & compliance (log query, export)
   - RBAC (roles, permissions, role assignment)
   - Support & KB (tickets, articles)
   Each endpoint definition must specify: method, path, auth scope/permission required,
   request/response schema, pagination convention, filtering/sorting convention, and
   standard error envelope.

3. GraphQL Layer
   - Define schema for read-heavy/aggregated use cases (dashboards, portfolio views, nested
     property→building→unit→lease queries) where REST would require excessive round-trips.
   - Specify resolver-level authorization enforcement matching RBAC scopes.
   - Define pagination (cursor-based), N+1 mitigation (dataloader pattern), and query
     complexity/depth limiting to prevent abuse.

4. Webhooks & Eventing
   - Define outbound webhook event catalog (lease.created, payment.received, maintenance.sla_breached,
     visitor.checked_in, document.expiring, etc.) with payload schemas, delivery retries,
     signing (HMAC), and replay/idempotency guarantees for subscribers.

5. API Gateway Concerns
   - Authentication: JWT validation, OAuth2/OIDC/SAML federation for enterprise SSO clients.
   - Rate limiting and throttling per tenant/API key/IP, with burst and sustained limits.
   - Request validation, payload size limits, and standardized error codes (problem+json style).
   - CORS policy, API key issuance/rotation for server-to-server integrations.
   - Multi-tenant routing — every authenticated request resolves to exactly one tenant context,
     derived server-side from the token, never from client-supplied headers/params alone.

6. Developer Experience
   - OpenAPI/GraphQL schema published and versioned; auto-generated client SDKs (TypeScript for
     web, Dart for Flutter) as a stretch goal.
   - Sandbox/staging environment with seeded multi-tenant test data.
   - Postman/Insomnia collection or equivalent for manual testing.

7. Integration Endpoints (third-party)
   - Payment gateway abstraction (mobile money, card processors, bank rails) with provider
     failover and dynamic routing.
   - SMS/Email/Push provider integration endpoints for the Notification Engine.
   - USSD gateway webhook/callback contract for inbound session events.
   - BI/data-warehouse export endpoints (JSON/CSV bulk export, change-data-capture feed).

DELIVERABLES
- OpenAPI 3.1 spec file(s) covering at least Auth, Property/Unit, Lease, and Payments domains.
- GraphQL SDL for the portfolio/dashboard aggregation use case.
- Webhook event catalog document with payload schemas and signing scheme.
- API gateway configuration (rate limits, auth middleware chain) as code/config.
- Error response standard (problem+json or equivalent) applied consistently across all specs.

ACCEPTANCE CRITERIA
- Every endpoint in the spec declares the exact permission/role required to call it.
- A request with a valid token for Tenant A cannot retrieve or mutate Tenant B's resources
  under any documented endpoint.
- Webhook payloads are versioned and signed; a sample consumer can verify signatures and
  safely retry on duplicate delivery without double-processing.
```

---

## 2. Work Plan

### Phase 0 — Contract Foundations (Weeks 1–2)
| Task | Output |
|---|---|
| Versioning strategy decision (URI vs. header-based), deprecation/sunset policy | ADR |
| Standard error envelope (problem+json), pagination & filtering conventions | API style guide |
| Auth scheme decision: JWT claims structure, OAuth2/OIDC/SAML federation approach | Auth contract spec |
| Tooling: OpenAPI 3.1 authoring workflow, spec linting (Spectral), spec-to-mock-server pipeline | CI-integrated spec validation |

**Exit criteria:** style guide and tooling approved; spec changes are lint-checked in CI before merge.

### Phase 1 — Core REST Contracts: Identity, Tenancy, RBAC (Weeks 3–5)
| Task | Output |
|---|---|
| Auth endpoints spec (login, refresh, MFA, logout, SSO callback) | `openapi/auth.yaml` |
| Tenancy/org admin endpoints (tenants, properties, buildings, floors, units, amenities) | `openapi/tenancy.yaml` |
| RBAC endpoints (roles, permissions, assignment, scoping) | `openapi/rbac.yaml` |
| Permission-to-endpoint mapping matrix (which role/action gates which route) | Permission matrix doc |
| Mock server stood up from specs for early frontend/mobile integration | Mock server deployed |

**Exit criteria:** frontend and mobile teams can integrate against mocked Auth/Tenancy/RBAC endpoints.

### Phase 2 — Lease, Rent Collection & Payments Contracts (Weeks 6–8)
| Task | Output |
|---|---|
| Tenant/lease lifecycle endpoints (leases, lease rules/fees, billing cycles) | `openapi/lease.yaml` |
| Payments endpoints (invoices, receipts, methods, reconciliation, refunds, arrears) | `openapi/payments.yaml` |
| Payment-gateway abstraction contract (provider-agnostic request/response, failover semantics) | `openapi/payment-providers.yaml` |
| Idempotency-key convention defined and applied to all mutating payment endpoints | Idempotency spec section |

**Exit criteria:** payment endpoints support idempotent retries; spec covers split/partial payment scenarios.

### Phase 3 — Finance, Utility & Service Charge Contracts (Weeks 9–10)
| Task | Output |
|---|---|
| Wallet & finance endpoints (balances, transactions, holds, expenses) | `openapi/wallet.yaml` |
| Utility billing & service charge endpoints (meters, tariffs, bills) | `openapi/utility.yaml` |

**Exit criteria:** wallet/utility specs reviewed against the backend ledger model for consistency.

### Phase 4 — Operational Modules: Maintenance, Vendor, Visitor, Documents (Weeks 11–13)
| Task | Output |
|---|---|
| Maintenance & vendor endpoints (requests, work orders, vendor assignment) | `openapi/maintenance.yaml` |
| Visitor management endpoints (registration, badges, check-in/out) | `openapi/visitor.yaml` |
| Documents endpoints (upload, metadata, versioning, signed URLs) | `openapi/documents.yaml` |

**Exit criteria:** signed-URL upload flow validated end-to-end with Supabase Storage.

### Phase 5 — GraphQL Aggregation Layer (Weeks 14–16)
| Task | Output |
|---|---|
| Identify read-heavy aggregation use cases (portfolio dashboards, nested property trees) | Use-case list |
| GraphQL SDL covering those use cases | `graphql/schema.graphql` |
| Resolver-level authorization design matching RBAC scopes | Authorization design doc |
| Pagination (cursor-based), dataloader/N+1 mitigation, query depth/complexity limiting | GraphQL gateway config |

**Exit criteria:** a single GraphQL query replaces what would otherwise be 3+ REST round-trips for a dashboard view, with auth enforced per-field/per-type.

### Phase 6 — Webhooks & Eventing (Weeks 17–18)
| Task | Output |
|---|---|
| Webhook event catalog (lease.*, payment.*, maintenance.*, visitor.*, document.* events) | Event catalog doc |
| Payload schema per event, HMAC signing scheme, retry/backoff policy | `openapi/webhooks.yaml` |
| Sample consumer implementation demonstrating signature verification + idempotent processing | Reference consumer repo/snippet |

**Exit criteria:** a third-party integrator can subscribe, verify signatures, and safely handle duplicate deliveries.

### Phase 7 — Gateway, Rate Limiting & Multi-Tenant Routing (Weeks 19–20)
| Task | Output |
|---|---|
| API gateway middleware chain: JWT validation, tenant-context resolution, rate limiting | Gateway config-as-code |
| Per-tenant/API-key/IP rate limit tiers (burst + sustained) | Rate-limit policy doc |
| CORS policy, API key issuance/rotation flow for server-to-server clients | API key management spec |
| USSD gateway callback contract (inbound session events) | `openapi/ussd-callback.yaml` |

**Exit criteria:** gateway rejects cross-tenant access attempts and enforces rate limits under load test.

### Phase 8 — Developer Experience & SDKs (Weeks 21–23)
| Task | Output |
|---|---|
| Publish versioned OpenAPI/GraphQL docs (e.g., via Redoc/Swagger UI/GraphiQL) | Public dev portal |
| Generate TypeScript client SDK (web) and Dart client SDK (mobile) from specs | Published SDK packages |
| Sandbox/staging environment with seeded multi-tenant test data | Sandbox environment |
| Postman/Insomnia collection covering all domains | Shared collection |

**Exit criteria:** frontend and mobile teams consume generated SDKs instead of hand-written HTTP clients.

### Phase 9 — Conformance Testing & Launch Readiness (Weeks 24–25)
| Task | Output |
|---|---|
| Contract tests verifying backend implementation matches published specs | Contract test suite (e.g., Dredd/Schemathesis) |
| Cross-tenant isolation penetration test against every documented endpoint | Security test report |
| Webhook delivery load/retry test | Reliability test report |

**Exit criteria:** all endpoints pass contract tests against the live backend; no cross-tenant data leak found.

### Dependencies & Sequencing Notes
- This track runs in lockstep with, but slightly ahead of, the [Backend](BACKEND.md) work plan — each API phase should land 1–2 weeks before the corresponding backend phase so mock servers unblock frontend/mobile early.
- GraphQL (Phase 5) and Webhooks (Phase 6) depend on the REST domain contracts (Phases 1–4) being stable, since they reuse the same underlying types.
- The USSD callback contract (Phase 7) must be finalized before the [USSD](USSD.md) work plan's Phase 2 begins.
