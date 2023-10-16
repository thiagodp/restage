# restage

> ✅ Add only modified files to the staging area

**Use case**: You're using Git and your files are ready to commit. But when you run `git commit`, your `pre-commit` Git Hook runs a formatting tool that change your files again. Now you have to run `git add` again, but only for the changed files, since you don't want to add untracked files yet. _That's boring, I know_. Fortunately, now you can use `restage` for that.

👉 `restage` add only modified files to the staging area.

## Install

> Requires only PHP 5.2+ and Git

```bash
composer require phputil/restage --dev
```

## Usage

```bash
php vendor/bin/restage
```

📖 Tip: You probably want to include to above command in a Git Hook such as `pre-commit`. You can also use [Captain Hook](https://github.com/captainhookphp/captainhook) to manage your Git Hooks within a JSON file.

## License

[MIT](LICENSE) © [Thiago Delgado Pinto](https://github.com/thiagodp)
