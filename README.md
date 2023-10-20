# restage

> ✅ Add only modified files to the staging area

**Use case**: You're using Git and your files are ready to commit. But when you run `git commit`, your `pre-commit` Git Hook runs a formatting tool that change your files again. Now you have to run `git add` again, but only for the changed files, since you don't want to add untracked files yet. _That's boring, I know_. Fortunately, now you can use `restage` for that.

👉 `restage` adds only modified files to the staging area.

## Install

> Requires only PHP 7.0+ and Git

```bash
composer require phputil/restage --dev
```

## Usage

```bash
php vendor/bin/restage
```

You probably want to include the above command into your Git Hook (e.g. `pre-commit`), to be executed after a command that formats your source code. For instance, `php vendor/bin/php-cs-fixer && php vendor/bin/restage`.

### CLI Options

```txt
  --help         This help.
  --all,      -a  List untracked files and modified staged files.
  --dry-run,  -d  Simulate the command without actually doing anything.
  --modified, -m  List modified staged files.
  --verbose,  -v  Enable verbose mode.
```

👉 If you wanna run a linter/formatter in modified files, you can use `--list` to get them.

### Useful tools

- [Captain Hook](https://github.com/captainhookphp/captainhook) can manage your Git Hooks.
- [PHP-CS-Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer) can format your source code.
- [PHP Code Sniffer](https://github.com/squizlabs/PHP_CodeSniffer) is an alternative to PHP-CS-Fixer.

## License

[MIT](LICENSE) © [Thiago Delgado Pinto](https://github.com/thiagodp)
