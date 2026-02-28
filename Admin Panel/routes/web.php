<?php

/*
|=============================================================================
| Plantix AI — Web Route Dispatcher
|=============================================================================
|
| This file is intentionally thin.  All panel-specific routes live in their
| own files under routes/panels/ so each panel can be reasoned about,
| tested, and maintained in isolation.
|
|  routes/panels/admin.php    — Admin Panel        (/admin/*)
|  routes/panels/vendor.php   — Vendor/Store Panel (/vendor/*)
|  routes/panels/expert.php   — Expert Panel       (/expert/*)
|  routes/panels/customer.php — User/Customer Panel (root)
|
| Authentication is handled per panel via dedicated Laravel guards:
|   admin guard   -> /admin/*
|   vendor guard  -> /vendor/*
|   expert guard  -> /expert/*
|   web guard     -> root  (customer / public)
|
| RBAC (Role-Based Access Control) is scoped to the Admin Panel only.
| See App\Services\Admin\RbacService for the business logic.
|
*/

require __DIR__ . '/panels/admin.php';
require __DIR__ . '/panels/vendor.php';
require __DIR__ . '/panels/expert.php';
require __DIR__ . '/panels/customer.php';
