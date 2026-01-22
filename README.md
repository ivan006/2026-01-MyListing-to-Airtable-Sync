
## Moderated Integration & Revision Service — Overview

This project is a **moderation-based integration system** between two separate portals:

* **Source (Contributor Portal):** WordPress using the *MyListing* theme
* **Target (Source of Truth):** Airtable (customer-facing data)

Contributors edit content in WordPress, but **nothing syncs automatically**. All data flows from WordPress → Airtable **only after moderator review and approval**.

---

## Core Architecture (by priority)

1. **Connector**
   Handles safe, directional data movement between WordPress and Airtable.

2. **Prompt System (Admin UI)**
   Allows moderators to select records, review changes, and approve syncs.

3. **External-ID Streamlining**
   Enables resolving both systems’ records from a single ID.

4. **Revision System (Later)**
   Tracks proposed changes and supports clearer moderation workflows.

---

## Initial Endpoints (Tasks)

* `configs-fetch` — expose allowed entities and mappings
* `target-fetch` — fetch Airtable record for review
* `source-fetch` — fetch WordPress/MyListing record (next)
* *(Later)* approved sync endpoints

---

**Key principle:** this is not freshness-based syncing; it is **approval-based integration**.
