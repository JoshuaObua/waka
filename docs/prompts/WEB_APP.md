# WAKA PMS — Web Admin Frontend / Web App

**Stack:** Vue 3, Nuxt 4, TypeScript, TailwindCSS, Pinia, Vue Query
**Audience:** Frontend engineering team / AI coding agent building the WAKA PMS web application

---

## 1. Prompt

```
You are a senior Vue/Nuxt frontend engineer building the web application for WAKA PMS — the
primary administrative and self-service surface for property owners, property management
companies, enterprise estate operators, support staff, and web-based tenant self-service.
This is the richest client of the WAKA API layer: where the mobile apps are persona-specific
and the USSD channel is menu-constrained, the web app must expose the platform's full
operational depth.

CONTEXT
WAKA must support single-owner landlords through enterprise multi-property portfolios from one
platform. The web app is where most configuration, financial oversight, and bulk operations
happen — it is the "control room" of the system, consumed by every internal role and by tenants
who prefer a browser over the mobile app.

TECH STACK
- Framework: Nuxt 4 (Vue 3, TypeScript) — SSR/hybrid rendering for fast first load and SEO where
  relevant (e.g., public-facing tenant invite/payment links)
- Styling: TailwindCSS with a shared design-token system (supports tenant-level white-labeling)
- State: Pinia for client/app state (auth session, UI state, RBAC-derived permissions)
- Data fetching/caching: Vue Query (TanStack Query) for all server-state — request
  deduplication, caching, optimistic updates, background refetch
- API integration: typed client generated from the WAKA OpenAPI/GraphQL specs; WebSocket
  client for realtime updates (notifications, live dashboards, ticket/maintenance status)
- Auth: JWT session via httpOnly cookies or secure storage, MFA challenge flows, SSO
  (OAuth2/OIDC/SAML) for enterprise tenants

INFORMATION ARCHITECTURE — BUILD DISTINCT WORKSPACES FOR THESE PERSONAS
1. Super Admin / Platform Console
   - Tenant provisioning and lifecycle (create/suspend/archive tenant accounts)
   - System-wide configuration, feature flags, integrations management
   - Global RBAC: role templates, permission category management, audit access
   - Platform-level analytics (cross-tenant usage, billing/subscription if applicable)

2. Owner / Property Manager Portal (primary workspace, richest feature set)
   - Property Management: category/type hierarchy admin, property/building/floor/unit CRUD,
     amenities, portfolio dashboards, occupancy/asset performance views
   - Tenant Management: onboarding pipeline (Kanban/stage view), KYC review, document
     verification, bulk import/export, tenant directory with filters
   - Lease Management: lease creation wizard, clause/rule builder, renewal/termination
     workflows, e-signature integration, lease document viewer with version compare
   - Rent Collection: invoice/receipt views, payment method configuration, reconciliation
     dashboard, arrears/aging reports, dunning notice management
   - Wallet & Finance: wallet balances/ledgers, expense entry and approval queues, budget
     vs. actual dashboards, GL/export tools
   - Utility Billing & Service Charge: meter reading entry/import, tariff configuration,
     consumption analytics, CAM allocation and reconciliation views
   - Maintenance & Vendor: work-order kanban/list, SLA dashboards, vendor directory and
     performance scorecards, contract management
   - Visitor Management: visitor log, badge configuration, watchlist management
   - Documents: document repository with versioning, expiry alerts, e-signature tracking
   - Reporting & Analytics: dashboard builder, scheduled report configuration, export tools,
     cross-property benchmarking views
   - Audit & Compliance: audit log explorer with filtering, compliance report generation
   - User & Role Management: user directory, role builder (drag-and-drop permission
     assignment), role simulation/preview before applying changes
   - Support & Knowledge Base: ticket queue, KB CMS, department routing configuration

3. Accountant / Finance Workspace (scoped view within the portal)
   - Focused finance dashboards, expense approval queues, ledger views, budget tracking

4. Tenant Web Portal (self-service, lighter than owner workspace)
   - Self-registration/KYC, lease/unit summary, invoice & payment history, online rent
     payment, wallet, maintenance request submission/tracking, document access, visitor
     guest registration, support ticket creation, profile/security settings

5. Support / Helpdesk Console
   - Ticket queue with SLA timers, KB management, escalation routing, response tracking

CROSS-CUTTING REQUIREMENTS
- Every screen and action must be gated by the user's RBAC permissions resolved from the
  backend — render nothing the user cannot act on, and hide/disable actions consistently
  with what the API will actually authorize (no client-only security).
- Multi-tenant awareness: a logged-in user only ever sees data for their authorized
  tenant/property/portfolio scope; no client-side tenant switching beyond what RBAC permits.
- All financial and bulk-action screens require confirmation modals and show clear
  success/error states; long-running bulk operations (imports, bulk billing runs) must show
  progress and be resumable/retry-safe.
- Real-time updates (via WebSocket) for dashboards, maintenance status, and notifications,
  with graceful fallback to polling if the socket connection drops.
- Responsive design: fully usable on tablet for on-site property managers, in addition to
  desktop.
- Accessibility: WCAG 2.1 AA target — keyboard navigation, screen reader labeling, sufficient
  contrast across all workspaces.
- Internationalization: multi-language UI, locale-aware currency/date/number formatting,
  configurable per tenant.
- White-labeling: tenant-level branding (logo, color palette, custom domain) without forking
  the codebase.

DELIVERABLES
- Information architecture / route map for all five workspaces with RBAC-gated navigation.
- Design-system component library (Tailwind-based) shared across workspaces.
- Auth module: login, MFA challenge, SSO, session/device management, permission-aware route
  guards and component-level permission directives/composables.
- Fully working vertical slice: Owner/Manager Portal — property → unit → lease → tenant
  onboarding → invoice → payment → reconciliation, wired to the real API with Vue Query.
- Realtime notification center component wired to the WebSocket channel.
- Reporting/dashboard module with at least one configurable, exportable report.

ACCEPTANCE CRITERIA
- A logged-in property manager sees only their authorized properties/portfolios and can
  complete the full lease-to-payment journey without a page reload breaking state.
- Navigating directly to a URL for an action outside the user's RBAC scope is blocked with a
  clear "not authorized" state, not a broken page or leaked data.
- A bulk tenant import of 500+ rows shows progress, reports row-level errors, and can be
  re-run safely without duplicating already-imported records.
- The tenant web portal allows a tenant to pay rent online and see the updated balance reflect
  identically in the mobile app and admin portal.
```

---

## 2. Work Plan

### Phase 0 — Foundation & Design System (Weeks 1–4)
| Task | Output |
|---|---|
| Nuxt 4 project scaffolding, TypeScript strict config, ESLint/Prettier, CI pipeline | Repo scaffolding |
| TailwindCSS design-token system with tenant-branding override mechanism | Design tokens + theming layer |
| Shared component library (buttons, forms, tables, modals, kanban primitives) | `packages/ui` library |
| Pinia store conventions; Vue Query client setup (caching, retry, error boundaries) | App-state + server-state conventions |
| Typed API client generated from the [API](API.md) team's OpenAPI/GraphQL specs | Generated client package |
| Auth module: login, MFA challenge, SSO entry points, route guards | Auth module |
| Permission resolution layer: composable/directive that reads RBAC permissions from the
  session and gates routes/components | `usePermission()` / `v-can` directive |

**Exit criteria:** an authenticated shell app loads, route guards correctly block unauthorized routes, and the design system is documented in a component playground (e.g., Storybook/Histoire).

### Phase 1 — Super Admin / Platform Console (Weeks 5–7)
| Task | Output |
|---|---|
| Tenant provisioning and lifecycle management UI | Tenant admin module |
| Global RBAC console: role templates, permission category management | RBAC admin module |
| System configuration and integrations management screens | Platform config module |
| Audit access and platform-level analytics views | Platform analytics module |

**Exit criteria:** a platform admin can provision a new tenant, assign an initial admin role, and see it reflected in tenant-scoped login.

### Phase 2 — Property, Unit & Tenant Management (Weeks 8–12)
| Task | Output |
|---|---|
| Property classification hierarchy admin (category/type/unit-category builder) | Classification module |
| Property/building/floor/unit CRUD with media gallery and amenities assignment | Property management module |
| Portfolio dashboards (occupancy, asset performance) | Portfolio dashboard |
| Tenant onboarding pipeline UI (stage view, KYC review, document verification) | Onboarding module |
| Bulk tenant/unit import/export with row-level error reporting and safe re-run | Bulk import module |

**Exit criteria:** a manager can build out a full property → building → unit tree and onboard a tenant through to KYC approval, with bulk import handling partial failures gracefully.

### Phase 3 — Lease & Rent Collection (Weeks 13–17)
| Task | Output |
|---|---|
| Lease creation wizard with clause/rule builder (penalties, escalation, billing cycle) | Lease module |
| Lease lifecycle screens: approval, renewal, termination, amendment, e-signature tracking | Lease lifecycle UI |
| Invoice/receipt views, payment method configuration, online payment flow | Payments module |
| Reconciliation dashboard, arrears/aging reports, dunning notice management | Collections module |

**Exit criteria:** the full vertical slice acceptance criterion is met — property → unit → lease → tenant onboarding → invoice → payment → reconciliation works end-to-end against the live API.

### Phase 4 — Finance: Wallet, Expenses, Utility & Service Charge (Weeks 18–21)
| Task | Output |
|---|---|
| Wallet balance/ledger views (tenant/owner/vendor/platform) | Wallet module |
| Expense entry and multi-level approval queue UI | Expense module |
| Budget vs. actual dashboards, GL/export tools | Budget module |
| Meter reading entry/import, tariff configuration, consumption analytics | Utility billing module |
| Service charge / CAM allocation and reconciliation views | Service charge module |
| Accountant/Finance scoped workspace (filtered view of the above) | Finance workspace |

**Exit criteria:** an accountant can approve an expense, configure a utility tariff, and see a budget-vs-actual dashboard update in real time.

### Phase 5 — Maintenance, Vendor, Visitor & Documents (Weeks 22–25)
| Task | Output |
|---|---|
| Work-order kanban/list view with SLA indicators | Maintenance module |
| Vendor directory, performance scorecards, contract management | Vendor module |
| Visitor log, badge configuration, watchlist management | Visitor module |
| Document repository with versioning, expiry alerts, e-signature tracking | Documents module |

**Exit criteria:** a manager can assign a maintenance request to a vendor, track it on the SLA dashboard to completion, and see the related document/contract trail.

### Phase 6 — Reporting, Audit & User/Role Management (Weeks 26–29)
| Task | Output |
|---|---|
| Configurable dashboard/report builder with scheduled export | Reporting module |
| Cross-property benchmarking views | Benchmarking dashboard |
| Audit log explorer with filtering and compliance report generation | Audit module |
| User directory and drag-and-drop role builder with role simulation/preview | Role builder module |

**Exit criteria:** an admin can build a custom report, schedule it, and preview the effect of a role change before applying it.

### Phase 7 — Support Console & Tenant Web Portal (Weeks 30–33)
| Task | Output |
|---|---|
| Support/helpdesk console: ticket queue, SLA timers, KB CMS, escalation routing | Support console |
| Tenant web portal: self-registration/KYC, lease/invoice/payment views, wallet, maintenance
  requests, document access, visitor guest registration, support tickets | Tenant portal |

**Exit criteria:** a tenant can complete the full self-service journey via browser (register → pay rent → raise a maintenance ticket), matching the mobile app's parity for these flows.

### Phase 8 — Realtime, Accessibility, i18n & Performance Hardening (Weeks 34–36)
| Task | Output |
|---|---|
| Realtime notification center wired to WebSocket, with polling fallback | Notification center |
| Accessibility audit and fixes (WCAG 2.1 AA) across all workspaces | Accessibility report |
| i18n pass: multi-language UI, locale-aware formatting | Localization complete |
| Performance pass: SSR/hydration tuning, bundle-size budget, list virtualization for large
  tables (tenants, transactions, audit logs) | Performance benchmark report |
| Tablet-responsive layout review for on-site property managers | Responsive QA report |

**Exit criteria:** all workspaces meet accessibility and performance targets and remain fully usable on tablet viewports.

### Phase 9 — Hardening & Launch Readiness (Weeks 37–38)
| Task | Output |
|---|---|
| Cross-tenant isolation UI testing (attempt to access out-of-scope routes/data) | Security test report |
| White-labeling validation across at least two tenant brand configurations | Branding QA report |
| End-to-end test suite covering the core journeys from each phase | E2E test suite (e.g., Playwright) |
| Production deployment pipeline (CI/CD, environment promotion, rollback) | Production deployment |

**Exit criteria:** no unauthorized data/route access found in testing; E2E suite passes in CI; production deployment is one-click/rollback-safe.

### Dependencies & Sequencing Notes
- Phase 0's generated API client depends on the [API](API.md) team publishing stable Auth/Tenancy/RBAC specs (their Phase 1) — this is the hardest blocker for the whole frontend track.
- Phase 3 (Lease & Rent Collection) depends on [Backend](BACKEND.md) Phase 3 being available in staging.
- Phase 7's Tenant Web Portal should reuse UX patterns and API contracts already proven by the [Mobile App](MOBILE_APP.md) Tenant App (their Phase 2) — build it second to capitalize on those learnings rather than designing tenant self-service twice from scratch.
- The Support Console (Phase 7) and the [Mobile App](MOBILE_APP.md) Admin/Support App (their Phase 6) consume the same ticketing backend module — coordinate to avoid divergent ticket-state assumptions.
- Realtime notification center (Phase 8) shares the WebSocket contract with [Backend](BACKEND.md) Phase 6 (Notification Engine) — confirm the event schema is frozen before building the UI binding.
