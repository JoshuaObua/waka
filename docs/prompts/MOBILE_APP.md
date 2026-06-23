# WAKA PMS — Mobile Applications (Flutter)

**Stack:** Flutter (Android & iOS), Riverpod/Bloc, REST/GraphQL/WebSocket client, FCM/APNs
**Audience:** Mobile engineering team / AI coding agent building the WAKA PMS mobile apps

---

## 1. Prompt

```
You are a senior Flutter engineer building the native mobile applications for WAKA PMS,
delivering persona-based experiences across Android and iOS from a shared Flutter codebase.

CONTEXT
The mobile apps are full-featured clients over the WAKA API layer (REST/GraphQL/WebSocket),
serving five distinct personas, each with a tailored experience but shared design system,
auth, and offline infrastructure.

TECH STACK
- Flutter (single codebase, Android + iOS targets)
- State management: your choice (Riverpod/Bloc) — justify the choice
- API integration: typed REST/GraphQL client against the WAKA API layer, WebSocket client for
  realtime updates (notifications, maintenance status, dashboard widgets)
- Local storage: secure local DB (e.g., Drift/Isar/Hive with encryption) for offline queueing
- Auth: JWT-based session with secure storage, biometric authentication (Face ID/fingerprint)
- Push notifications: FCM (Android) / APNs (iOS) with deep-link routing
- Analytics & crash reporting integrated from day one

CROSS-PERSONA FOUNDATION (build once, reuse everywhere)
- Authentication flows: login, MFA (OTP/biometric), password reset, session/device management,
  SSO where applicable.
- Offline support: local request queueing with automatic retry/sync when connectivity returns;
  conflict resolution strategy for data edited offline.
- Push notifications with deep-link support routing directly to the relevant screen
  (e.g., a payment-reminder push opens the invoice detail screen).
- Mobile-first forms and payment flows (multi-step, validated, resumable).
- App health checks, performance monitoring, and crash reporting wired into CI.
- Theming/design system shared across personas, with tenant-level custom branding support.

PERSONA-SPECIFIC EXPERIENCES TO BUILD
1. Tenant App
   - Self-service onboarding & KYC capture (document upload, identity verification)
   - Home dashboard: lease, payment, and notification summary
   - Invoice/payment screens supporting the platform's full payment-method set (mobile money,
     card, wallet, bank transfer)
   - Digital wallet management (balance, top-up, transaction history)
   - Maintenance request creation with photo/video attachment and status tracking
   - Document library access (lease, statements, receipts)
   - Visitor guest registration and access-pass generation (QR code)
   - Community announcements/notices feed
   - Profile and security settings (MFA, biometric toggle, device management)
   - Support ticket creation and knowledge-base/FAQ browsing

2. Owner / Manager App
   - Portfolio overview across properties with occupancy/revenue KPIs
   - Property, unit, and lease management (create/edit, status changes)
   - Tenant onboarding review and approval workflows
   - Payment collection and reconciliation views
   - Maintenance and vendor oversight (assign, track SLA, approve work orders)
   - Reports & analytics snapshots (drill-down where feasible on mobile)
   - User and role access controls (assign roles, scoped to property/portfolio)
   - Alerts and task management inbox

3. Maintenance Team App
   - Assigned work-order list with priority/SLA indicators
   - Ticket detail view with full request history and attachments
   - Maintenance checklist execution (structured forms per request type)
   - Photo/attachment capture at point of service
   - Status update and completion report submission
   - In-app communication thread with tenant/manager

4. Visitor App
   - Guest registration and pre-authorization request
   - QR code / badge generation and display for entry
   - Visit scheduling with host notification
   - Access history and post-visit feedback capture

5. Admin / Support App
   - Ticket queue with SLA dashboard and priority sorting
   - Knowledge base and FAQ content management (lightweight CMS view)
   - Department routing and escalation controls
   - Ticket assignment and response tracking
   - Audit/compliance checklist views

NON-FUNCTIONAL REQUIREMENTS
- Secure local storage: encrypt any cached PII, payment tokens, and documents at rest on-device.
- Biometric authentication required for sensitive actions (payments, profile/security changes).
- Accessibility: screen-reader support, scalable text, sufficient color contrast (WCAG 2.1 AA
  target) across all five persona apps.
- Internationalization: support multiple languages and locale-aware currency/date/number
  formatting, configurable per tenant.
- Performance: cold-start time, list virtualization for large datasets (transaction history,
  ticket lists), and image/document lazy-loading.
- Resilience: clear offline indicators, queued-action visibility, and graceful retry messaging
  when backend/API calls fail.

DELIVERABLES
- App architecture document (module structure, navigation/routing, state management layer,
  shared design-system package).
- Working build of at least the Tenant App's core flow: login → dashboard → view invoice →
  pay rent → see updated wallet balance → receive push confirmation.
- Offline queueing implementation demonstrated against at least one write action (e.g.,
  maintenance request creation while offline, synced on reconnect).
- CI pipeline producing signed Android and iOS builds, with crash reporting and analytics wired in.

ACCEPTANCE CRITERIA
- A tenant can complete the full onboarding-to-payment journey without backend errors, with
  the payment visible identically via the web portal.
- An action performed offline is queued, clearly indicated as pending, and successfully
  syncs once connectivity is restored, without data loss or duplication.
- Each persona app only exposes screens and actions permitted by that user's RBAC role.
```

---

## 2. Work Plan

### Phase 0 — Foundation & Shared Infrastructure (Weeks 1–4)
| Task | Output |
|---|---|
| Flutter project scaffolding: module structure, flavor setup (per persona/tenant branding) | Repo scaffolding |
| State management decision (Riverpod/Bloc) and justification | ADR |
| Generated API client from the [API](API.md) team's OpenAPI/GraphQL specs | Typed REST/GraphQL client package |
| Secure local storage layer (encrypted DB) for offline queueing | Local storage module |
| Auth module: login, MFA (OTP/biometric), secure token storage, session/device management | Shared auth package |
| Shared design system (theming, typography, components) with tenant-branding hooks | Design system package |
| CI pipeline: build, lint, test, signed Android/iOS artifacts | CI pipeline |
| Crash reporting & analytics SDK integration | Wired into app shell |

**Exit criteria:** an empty shell app builds for both platforms via CI, supports login with MFA, and reports crashes/analytics.

### Phase 1 — Offline & Realtime Infrastructure (Weeks 5–6)
| Task | Output |
|---|---|
| Offline write-queue with automatic retry/sync on reconnect | Offline sync engine |
| Conflict-resolution strategy for data edited offline | Conflict resolution policy + implementation |
| WebSocket client for realtime updates (notifications, ticket/maintenance status) | Realtime client module |
| Push notification setup (FCM/APNs) with deep-link routing table | Push + deep-link infrastructure |

**Exit criteria:** a write action performed offline queues, syncs on reconnect without duplication, and a push notification deep-links into the correct screen.

### Phase 2 — Tenant App (Weeks 7–12)
| Task | Output |
|---|---|
| Onboarding & KYC capture flow (document upload, identity verification) | Onboarding flow |
| Home dashboard (lease/payment/notification summary) | Dashboard screen |
| Invoice/payment screens across the full payment-method set | Payment flow |
| Digital wallet screens (balance, top-up, history) | Wallet module |
| Maintenance request creation + tracking (photo/video attachment) | Maintenance module |
| Document library, visitor guest registration/QR pass, community announcements | Remaining tenant screens |
| Profile/security settings, support ticket creation, KB browsing | Profile + support module |

**Exit criteria:** full Tenant App acceptance criterion met — login → dashboard → pay rent → updated wallet balance → push confirmation, end to end against the real backend.

### Phase 3 — Owner / Manager App (Weeks 13–17)
| Task | Output |
|---|---|
| Portfolio overview with occupancy/revenue KPIs | Portfolio dashboard |
| Property/unit/lease management screens | Management module |
| Tenant onboarding review/approval workflow | Approval flow |
| Payment collection/reconciliation views | Finance views |
| Maintenance/vendor oversight (assign, SLA tracking, approvals) | Oversight module |
| Reports/analytics snapshots, user/role access controls, alerts/task inbox | Remaining manager screens |

**Exit criteria:** an owner/manager can review and approve a tenant onboarding, assign a maintenance ticket to a vendor, and view a reconciled payment report — all scoped to their RBAC role.

### Phase 4 — Maintenance Team App (Weeks 18–20)
| Task | Output |
|---|---|
| Assigned work-order list with priority/SLA indicators | Work order list |
| Ticket detail, checklist execution, photo/attachment capture | Field execution flow |
| Status update/completion report submission, in-app communication thread | Completion + comms module |

**Exit criteria:** a technician can receive an assignment, execute a checklist with photo evidence, and close the ticket with a completion report visible to the manager app in realtime.

### Phase 5 — Visitor App (Weeks 21–22)
| Task | Output |
|---|---|
| Guest registration/pre-authorization request flow | Registration flow |
| QR/badge generation and display | Badge module |
| Visit scheduling with host notification, access history, feedback capture | Remaining visitor screens |

**Exit criteria:** a visitor can pre-register, receive host approval, and present a QR badge that is validated at entry.

### Phase 6 — Admin / Support App (Weeks 23–24)
| Task | Output |
|---|---|
| Ticket queue/SLA dashboard, KB/FAQ lightweight CMS view | Support dashboard |
| Department routing/escalation controls, assignment/response tracking | Routing module |
| Audit/compliance checklist views | Compliance module |

**Exit criteria:** a support agent can triage, route, and resolve a ticket, with full audit visibility.

### Phase 7 — Accessibility, Localization & Performance Hardening (Weeks 25–26)
| Task | Output |
|---|---|
| Accessibility audit and fixes (screen reader, contrast, scalable text) across all 5 apps | WCAG 2.1 AA compliance report |
| i18n pass: multi-language support, locale-aware currency/date/number formatting | Localization complete |
| Performance pass: cold-start time, list virtualization, image/document lazy-loading | Performance benchmark report |

**Exit criteria:** all five persona apps pass the accessibility audit and meet defined cold-start/performance targets.

### Phase 8 — Beta, Store Submission & Launch (Weeks 27–28)
| Task | Output |
|---|---|
| Closed beta across all 5 personas with real tenant data (staging) | Beta feedback report |
| App Store / Play Store submission (metadata, screenshots, compliance review) | Store listings submitted |
| Production monitoring dashboards (crash rate, API error rate, adoption) | Launch monitoring setup |

**Exit criteria:** all five apps are live in their respective stores with monitoring in place.

### Dependencies & Sequencing Notes
- Phase 0's generated API client depends on the [API](API.md) team publishing stable OpenAPI/GraphQL specs for Auth, Tenancy, RBAC (their Phase 1).
- Tenant App (Phase 2) payment flows depend on [Backend](BACKEND.md) Phase 3 (Lease & Rent Collection) being live in staging.
- Owner/Manager App (Phase 3) and Maintenance Team App (Phase 4) can run in parallel once Phase 1 (offline/realtime infra) is complete, since they touch largely independent backend domains.
- Visitor App (Phase 5) depends on [Backend](BACKEND.md) Phase 5 (Visitor Management).
