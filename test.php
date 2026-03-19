<?php
define('CLI_SCRIPT', true);

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->libdir . '/weblib.php');

global $DB;

$courseid = 71241;

$table = 'label';
$field = 'intro';

list($options, $unrecognized) = cli_get_params([
    'dry-run' => false,
    'since'   => null,
    'limit'   => 500,
    'help'    => false
]);

if ($options['help']) {
    echo "Clean mdl_url.intro HTML using Moodle's HTMLPurifier\n";
    echo "--dry-run        Show changes without saving\n";
    echo "--since=TIMESTAMP Only process records modified after timestamp\n";
    echo "--limit=N        Batch size (default 500)\n";
    exit(0);
}

$limit = (int)$options['limit'];
$since = $options['since'];
$dryrun = $options['dry-run'];

echo "Starting $table $field tidy process...\n";
echo $dryrun ? "DRY RUN MODE\n" : "LIVE MODE\n";

$params = [];
$where = "course = $courseid AND $field IS NOT NULL AND $field <> ''";

if ($since) {
    $where .= " AND timemodified > :since";
    $params['since'] = (int)$since;
}

$totalprocessed = 0;
$totalupdated = 0;
$offset = 0;

do {
    $records = $DB->get_records_select(
        'label',
        $where,
        $params,
        'id ASC',
        '*',
        $offset,
        $limit
    );

    foreach ($records as $record) {

$original = $record->$field;

        $wrapped_html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>' . $original . '</body></html>';

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        
        $dom->loadHTML($wrapped_html, LIBXML_HTML_NODEFDTD);
        
        $body = $dom->getElementsByTagName('body')->item(0);
        $cleaned = '';
        
        if ($body) {
            foreach ($body->childNodes as $child) {
                $cleaned .= $dom->saveHTML($child);
            }
        }
        
        libxml_clear_errors();

        mtrace("\noriginal\n");
        var_dump($original);
        mtrace("\ncleaned\n");
        var_dump($cleaned);

        $reallycleaned = clean_text($cleaned, FORMAT_HTML);
        mtrace("\nreallycleaned\n");
        var_dump($reallycleaned);

        if ($cleaned !== $original) {
            $totalupdated++;

            // If we are testing.
            if (!$dryrun) {
                $record->$field = $cleaned;
                $record->timemodified = time();
                $DB->update_record($table, $record);
            }

            echo "Updated URL ID {$record->id}\n";
        }

        $totalprocessed++;
    }

    $offset += $limit;

} while (!empty($records));

// Invalidate only this course's modinfo cache
rebuild_course_cache($courseid, true);

echo "Done.\n";
echo "Processed: $totalprocessed\n";
echo "Updated: $totalupdated\n";
