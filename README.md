# IRIS — Ivan’s Robust Integration System

## Philosophy

**IRIS is a relationship-first integration system.**

Most integrations sync records.
Very few sync **relationships** — even though relationships are a **critical** part of any real integration.

IRIS exists to treat relationships as first-class concerns.

---

### What IRIS Does

**Relationship Syncing (Primary)**
IRIS syncs relationships by assigning entities a stable internal identity and translating foreign keys between systems at sync time, rather than leaking IDs across schemas.
This allows each system to use its own native identifiers while relationships remain correct.

**Revision Tracking (Secondary)**
Because entities have stable identity, IRIS can diff changes, commit them, and maintain a revision history.
This capability naturally emerges from the same foundation used for relationship syncing.

**Intervention Mode (Optional)**
IRIS can require **moderator approval** before changes are committed, enabling controlled, review-based integrations.

---

### Notes

* IRIS also supports **image and media syncing** (adapter-specific where necessary)
* The system is **target- and source-agnostic**, where practical
