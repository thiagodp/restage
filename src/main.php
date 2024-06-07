<?php
namespace phputil\restage;

const TOOL = '[restage] ';
const SUCCESS = 0;
const MAX_TRIES = 100;

function successColor( $msg ) { return "\033[0;32m$msg\033[0m"; }

function hasFlag( $flag ) {
    if ( ! isset( $_SERVER[ 'argv' ] ) ) {
        return false;
    }
    $index = array_search( $flag, $_SERVER[ 'argv' ] );
    return $index !== false;
}

function showHelp() {
    echo TOOL, PHP_EOL;
    echo '  --help          This help.', PHP_EOL;
    echo '  --all,      -a  List untracked files and modified staged files.', PHP_EOL;
    echo '  --dry-run,  -d  Simulate the command without actually doing anything.', PHP_EOL;
    echo '  --modified, -m  List modified staged files.', PHP_EOL;
    echo '  --verbose,  -v  Enable verbose mode.', PHP_EOL;
    exit( SUCCESS );
}

function findGitDirectory() {
    $gitDir = '.git';
    $sep = DIRECTORY_SEPARATOR;
    $dir = getcwd();
    $current = "{$dir}{$sep}{$gitDir}";
    $tries = 0;
    while ( ! @file_exists( $current ) && $tries < MAX_TRIES ) {
        $dir = dirname( $dir );
        $current = "{$dir}{$sep}{$gitDir}";
        $tries++;
    }
    return $current;
}

function main() {

    $helpMode           = hasFlag( '--help' );
    $verboseMode        = hasFlag( '--verbose' )  || hasFlag( '-v' );
    $dryRunMode         = hasFlag( '--dry-run' )  || hasFlag( '-d' );
    $listModifiedMode   = hasFlag( '--modified' ) || hasFlag( '-m' );
    $listAllMode        = hasFlag( '--all' )      || hasFlag( '-a' );

    if ( $helpMode ) {
        showHelp();
    }
    if ( $dryRunMode ) {
        echo TOOL, 'Dry-run enabled', PHP_EOL;
    }

    $command = 'git status --porcelain';
    if ( $verboseMode || $dryRunMode ) {
        echo TOOL, $command, PHP_EOL;
    }
    exec( $command, $output, $exitCode );
    if ( $exitCode != SUCCESS ) {
        echo TOOL, "Could not run \"$command\"", PHP_EOL;
        exit( $exitCode );
    }

    // Extract files
    $modifiedFiles = [];
    $untrackedFiles = [];
    foreach ( $output as $line ) {
        // It also works with multi-byte strings
        $mode = trim( substr( $line, 0, 2 ) );
        $file = trim( substr( $line, 2 ) );
        if ( $mode == 'M' || $mode == 'MM' ) {
            $modifiedFiles []= $file;
        } else if ( $mode == '?' || $mode == '??' ) {
            $untrackedFiles []= $file;
        }
    }

    if ( $listModifiedMode ) {
        echo implode( ' ', $modifiedFiles );
        exit( SUCCESS );
    }
    if ( $listAllMode ) {
        echo implode( ' ', array_merge( $modifiedFiles, $untrackedFiles ) );
        exit( SUCCESS );
    }

    // There are no modified files
    if ( count( $modifiedFiles ) < 1 ) {
        echo TOOL, 'Nothing to do.', PHP_EOL;
        exit( SUCCESS );
    }

    $dir = dirname( findGitDirectory() );
    $sep = DIRECTORY_SEPARATOR;
    foreach ( $modifiedFiles as &$file ) {
        if ( $verboseMode ) {
            echo TOOL, $file, ' => ', "{$dir}{$sep}{$file}", PHP_EOL;
        }
        $file = "{$dir}{$sep}{$file}";
    }

    $command = 'git add ' . implode( ' ', $modifiedFiles );
    if ( $verboseMode || $dryRunMode ) {
        echo TOOL, $command, PHP_EOL;
    }
    if ( ! $dryRunMode ) {
        exec( $command, $output, $exitCode );
        if ( $exitCode != SUCCESS ) {
            echo TOOL, "Could not run \"$command\"", PHP_EOL;
            exit( $exitCode );
        }
    }

    echo TOOL, successColor( implode( ' ', $modifiedFiles ) ), PHP_EOL;
    exit( SUCCESS );
}

main();
?>