# Run PHP Unit Tests
workflow "PHP Tests" {
  on = "push"
  resolves = ["PHPTestUnit"]
}

action "PHPInstall" {
  uses = "MilesChou/composer-action@master"
  args = "install"
}

action "PHPTestUnit" {
  uses = "MilesChou/composer-action@master"
  needs = "PHPInstall"
  args = "test:unit"
}
