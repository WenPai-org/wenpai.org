Feature: Check for more recent versions

  # This test downgrades to an older WordPress version, but the SQLite plugin requires 6.0+
  @require-mysql
  Scenario: Check for update via Version Check API
    Given a WP install
    And I try `wp theme install twentytwenty --activate`

    When I run `wp core download --version=5.8 --force`
    Then STDOUT should not be empty

    When I run `wp core check-update`
    Then STDOUT should be a table containing rows:
      | version                 | update_type | package_url                                                                             |
      | {WP_VERSION-latest}     | major       | https://downloads.wordpress.org/release/wordpress-{WP_VERSION-latest}.zip               |
      | {WP_VERSION-5.8-latest} | minor       | https://downloads.wordpress.org/release/wordpress-{WP_VERSION-5.8-latest}-partial-0.zip |

    When I run `wp core check-update --format=count`
    Then STDOUT should be:
      """
      2
      """

    When I run `wp core check-update --major`
    Then STDOUT should be a table containing rows:
      | version             | update_type | package_url                                                               |
      | {WP_VERSION-latest} | major       | https://downloads.wordpress.org/release/wordpress-{WP_VERSION-latest}.zip |

    When I run `wp core check-update --major --format=count`
    Then STDOUT should be:
      """
      1
      """

    When I run `wp core check-update --minor`
    Then STDOUT should be a table containing rows:
      | version                 | update_type | package_url                                                                             |
      | {WP_VERSION-5.8-latest} | minor       | https://downloads.wordpress.org/release/wordpress-{WP_VERSION-5.8-latest}-partial-0.zip |

    When I run `wp core check-update --minor --format=count`
    Then STDOUT should be:
      """
      1
      """
