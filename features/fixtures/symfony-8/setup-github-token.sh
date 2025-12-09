if [ -n "$COMPOSER_GITHUB_TOKEN" ]; then
    composer config --global github-oauth.github.com "$COMPOSER_GITHUB_TOKEN"
fi
