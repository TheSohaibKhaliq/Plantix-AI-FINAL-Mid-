#!/usr/bin/env node
/**
 * export_firebase.js
 *
 * Exports all Firestore collections to JSON files that the Laravel Artisan
 * import commands can then read and INSERT into MySQL.
 *
 * USAGE:
 *   npm install firebase-admin
 *   node export_firebase.js
 *
 * Set GOOGLE_APPLICATION_CREDENTIALS to your service-account JSON path, or
 * pass it as the first CLI argument:
 *   node export_firebase.js /path/to/serviceAccount.json
 *
 * Output:  ./firebase_export/<collection>.json  (one file per collection)
 */

const admin  = require('firebase-admin');
const fs     = require('fs');
const path   = require('path');

// ── service account ──────────────────────────────────────────────────────────
const credPath = process.argv[2]
    || process.env.GOOGLE_APPLICATION_CREDENTIALS
    || path.join(__dirname, 'storage/app/firebase/credentials.json');

const serviceAccount = require(credPath);

admin.initializeApp({
    credential: admin.credential.cert(serviceAccount),
});

const db      = admin.firestore();
const outDir  = path.join(__dirname, 'firebase_export');

if (!fs.existsSync(outDir)) fs.mkdirSync(outDir, { recursive: true });

// ── Firestore collections to export ─────────────────────────────────────────
const COLLECTIONS = [
    'users',
    'vendors',
    'vendor_products',
    'orders',
    'coupons',
    'payouts',
    'wallet',
    'foods_review',
    'favorite_restaurant',
    'favorite_item',
    'booked_table',
    'story',
    'documents',
    'documents_verify',
    'zone',
    'settings',
    'transactions',
];

async function exportCollection(collectionName) {
    console.log(`Exporting collection: ${collectionName}...`);
    const snapshot = await db.collection(collectionName).get();

    const docs = snapshot.docs.map((doc) => {
        const data = doc.data();
        // Convert Firestore Timestamps to ISO strings
        const serialized = JSON.parse(
            JSON.stringify(data, (key, value) => {
                if (value && typeof value === 'object' && value._seconds !== undefined) {
                    // Firestore Timestamp
                    return new Date(value._seconds * 1000).toISOString();
                }
                if (value && typeof value === 'object' && value._latitude !== undefined) {
                    // Firestore GeoPoint
                    return { lat: value._latitude, lng: value._longitude };
                }
                return value;
            })
        );
        return { _id: doc.id, ...serialized };
    });

    const outFile = path.join(outDir, `${collectionName}.json`);
    fs.writeFileSync(outFile, JSON.stringify(docs, null, 2), 'utf8');
    console.log(`  ✓ Exported ${docs.length} documents → ${outFile}`);
}

(async () => {
    try {
        for (const col of COLLECTIONS) {
            await exportCollection(col);
        }
        console.log('\n✅ Firebase export complete. Files are in ./firebase_export/');
        console.log('   Run Laravel commands: php artisan firebase:import-users, etc.');
    } catch (err) {
        console.error('Export failed:', err);
        process.exit(1);
    }
})();
