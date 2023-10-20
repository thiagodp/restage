<?php
namespace phputil\restage;

const TOOL = '[restage] ';
const SUCCESS = 0;

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

function main() {

    $helpMode     = hasFlag( '--help' );
    $verboseMode  = hasFlag( '--verbose' )  || hasFlag( '-v' );
    $dryRunMode   = hasFlag( '--dry-run' )  || hasFlag( '-d' );
    $modifiedMode = hasFlag( '--modified' ) || hasFlag( '-m' );
    $allMode      = hasFlag( '--all' )      || hasFlag( '-a' );

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
        // Works with multi-byte strings
        $mode = trim( substr( $line, 0, 2 ) );
        $file = trim( substr( $line, 2 ) );
        if ( $mode == 'M' || $mode == 'MM' ) {
            $modifiedFiles []= $file;
        } else if ( $mode == '?' || $mode == '??' ) {
            $untrackedFiles []= $file;
        }
    }

    if ( $modifiedMode ) {
        echo implode( ' ', $modifiedFiles );
        exit( SUCCESS );
    }
    if ( $allMode ) {
        echo implode( ' ', array_merge( $modifiedFiles, $untrackedFiles ) );
        exit( SUCCESS );
    }

    // No modified files
    if ( count( $modifiedFiles ) < 1 ) {
        echo TOOL, 'Nothing to do.', PHP_EOL;
        exit( SUCCESS );
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
?>