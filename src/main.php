<?php
namespace phputil\restage;

const TOOL = '[restage] ';
const SUCCESS = 0;

function successColor( $msg ) { return "\033[1;37m\033[42m$msg\033[0m"; }

function hasFlag( $flag ) {
    if ( ! isset( $_SERVER[ 'argv' ] ) ) {
        return false;
    }
    $index = array_search( $flag, $_SERVER[ 'argv' ] );
    return $index !== false;
}

function showHelp() {
    echo TOOL, PHP_EOL;
    echo '  --help         This help.', PHP_EOL;
    echo '  --dry-run, -d  Simulate the command without actually doing anything.', PHP_EOL;
    echo '  --list,    -l  List modified staged files.', PHP_EOL;
    echo '  --verbose, -v  Enable verbose mode.', PHP_EOL;
    exit( SUCCESS );
}

function main() {

    $verboseMode = hasFlag( '-v' ) || hasFlag( '--verbose' );
    $dryRunMode = hasFlag( '-d' ) || hasFlag( '--dry-run' );
    $listMode = hasFlag( '-l' ) || hasFlag( '--list' );
    $helpMode = hasFlag( '--help' );

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

    // Extract changed files
    $changedFiles = [];
    foreach ( $output as $line ) {
        // Works with multi-byte strings
        $mode = trim( substr( $line, 0, 2 ) );
        $file = trim( substr( $line, 2 ) );
        if ( $mode == 'M' || $mode == 'MM' ) {
            $changedFiles []= $file;
        }
    }

    if ( $listMode ) {
        echo implode( ' ', $changedFiles );
        exit( SUCCESS );
    }

    // No changes
    if ( count( $changedFiles ) < 1 ) {
        if ( $verboseMode ) {
            echo TOOL, 'No changes.', PHP_EOL;
        }
        echo TOOL, successColor( ' OK ' ), PHP_EOL;
        exit( SUCCESS );
    }

    $command = 'git add ' . implode( ' ', $changedFiles );
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

    echo TOOL, successColor( ' OK ' ), PHP_EOL;
    exit( SUCCESS );
}
?>