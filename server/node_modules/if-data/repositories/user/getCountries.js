var db = require('if-data').db;
var Q = require('q');

function getContries() {
    var sql = "SELECT  country_lookup.country_name, country_lookup.id FROM country_lookup ORDER BY  country_lookup.country_name";
    return Q.nfcall(db.query, sql);
}

module.exports = getContries;