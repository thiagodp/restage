<?php
const TOOL = '[restage] ';
const SUCCESS = 0;
$command = 'git status --porcelain';
exec( $command, $output, $exitCode );
if ( $exitCode != SUCCESS ) {
    echo TOOL, "Could not run \"$command\"", PHP_EOL;
    exit( $exitCode );
}
$changedFiles = [];
// In this case, substr() will do the job for multi-byte strings
foreach ( $output as $line ) {
    if ( substr( $line, 0, 2 ) == ' M' ) {
        $changedFiles []= substr( $line, 3 );
    }
}
$command = 'git add ' . implode( ' ', $changedFiles );
exec( $command, $output, $exitCode );
if ( $exitCode != SUCCESS ) {
    echo TOOL, "Could not run \"$command\"", PHP_EOL;
    exit( $exitCode );
}
function colorful( $msg ) { return "\033[1;37m\033[42m$msg\033[0m"; }
echo TOOL, colorful( ' OK ' );
?>