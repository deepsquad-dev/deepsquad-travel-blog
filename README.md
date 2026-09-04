# deepsquad

Invite-only travel adventures on a map, plus a blog. CakePHP 5 rebuild of the old deepsquad / Bloggery experiments.

## Feature map

| Area | Status | Notes |
|------|--------|-------|
| Auth (login / logout) | Done | No open signup; owner bootstrap CLI |
| Invite-only friends | Done | Per-trip token invites, accept + password |
| Trips CRUD | Done | Create, view, edit, delete |
| Trip dates / visibility | Done | starts/ends, private / friends / public |
| Map pins + media | Done | Leaflet/OSM; photo/video; click map to add |
| Pin edit / delete / media manage | Done | Add more media, remove files |
| Travellers (members) | Done | List, change editor/viewer, remove |
| Invite list / revoke | Done | Pending invites on trip + invite page |
| Blog (Bloggery) | Done | Posts, tags, search, RSS, optional trip link |
| Portfolio / Work | Not yet | Legacy CI/Cake portfolio |
| Contact / Downloads | Not yet | Legacy forms & files |
| Legal pages | Not yet | Privacy / cookies / terms |
| Standalone photo gallery | Partial | Media lives on pins |
| Real invite email | Partial | Link in flash; Debug transport |

## Run (native PHP)

```bash
cd deepsquad
./bin/ds migrations migrate
./bin/serve                # http://localhost:8765
./bin/ds bootstrap_owner you@example.com "Your Name" 'your-password'
```

`./bin/ds` loads SQLite from `tools/php-ext` if `php-sqlite3` is not installed system-wide.

## Trip flow

1. Log in → **Start a trip**
2. **Invite a friend** (copy link from flash / manage pending invites)
3. Click the map → add pin + media
4. Edit trip, pins, traveller roles from the trip page

## Blog flow

1. `/blog` — public & friends-visible posts
2. Owner: **Write a post**, tags, link a trip, RSS at `/blog/rss`

## Stack

- CakePHP 5.4 + Authentication
- SQLite (`tmp/deepsquad.sqlite`)
- Leaflet / OpenStreetMap

Local owner (if seeded): `owner@deepsquad.test` / `changeme123`
