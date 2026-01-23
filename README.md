
## Moderated Integration & Revision Service — Overview

This project is an **approval-based integration system** between two separate portals:

* **Source (Contributor Portal):** WordPress using the *MyListing* theme
* **Target (Source of Truth):** Airtable (customer-facing data)

Contributors edit content in WordPress, but **nothing syncs automatically**. All data flows from WordPress → Airtable **only after moderator review and approval**.

---

## Core Architecture (by priority)

1. **Connector**
   Handles safe, directional data movement between WordPress and Airtable.
   Responsible for fetching records from each system and enforcing allowed access.

2. **Prompt System (Admin UI)**
   Provides a human-facing interface to:

   * select source and target records
   * review normalized data
   * inspect differences
   * approve or reject changes

3. **External-ID Streamlining**
   A usability and data-entry simplification layer.
   Instead of requiring administrators to repeatedly specify matching record IDs across systems, this milestone:

   * allows source and target records to be linked once
   * stores the corresponding external ID on each system’s record
   * removes the need to memorise or re-enter IDs in the UI

   This does **not** introduce a central registry; it simply reduces friction by persisting known foreign/external IDs at the system level.

4. **Revision & Relationship Sync Support (Optional)**
   Builds on the earlier layers to enable:

   * diff computation and optional diff persistence
   * revision history
   * **robust cross-system relational syncing**

   This milestone introduces **Registered Entities (the Entity Registry)** as core infrastructure, providing:

   * stable internal identities for real-world entities
   * mapping between source and target external IDs
   * translation of foreign keys between systems
   * a foundation for revision tracking and relationship syncing

---

## Initial Endpoints (Tasks)

* `configs-fetch` — expose allowed entities and field mappings
* `source-fetch` — fetch WordPress/MyListing records
* `target-fetch` — fetch Airtable records
* *(Later)* approved sync endpoints

---

**Key principle:** this system prioritizes **explicit approval and relational integrity** over automatic or freshness-based syncing.
