<?php
/**
 * Translation package import HTML
 *
 * @package wpfanyi-import
 */

defined('ABSPATH') || exit;
?>
<div class="wrap">
  <h1>
      <?php esc_html_e('Import translation', 'wpfanyi-import'); ?>
  </h1>
  <div>
    <div class="notice notice-info">
      <p>
          <?php esc_html_e('The translation package is a Zip package including .mo and .po files. Select the translation pack on this page and set its type correctly then click Import to add it to WordPress.', 'wpfanyi-import'); ?>
        <br/>
        <b>
            <?php esc_html_e('Note: If a translation package with the same name already exists, this operation will overwrite it', 'wpfanyi-import'); ?>
        </b></p>
    </div>
    <div class="main">
      <div class="function">
        <ul class="wpfanyi-tab-title">
          <li class="wpfanyi-this">
              <?php esc_html_e('Import from Local', 'wpfanyi-import'); ?>
          </li>
          <li>
              <?php esc_html_e('Import from URL', 'wpfanyi-import'); ?>
          </li>
        </ul>
        <div class="wpfanyi-tab-content">
          <div class="wpfanyi-tab-item wpfanyi-show">
            <form method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('wpfanyi-import-nonce'); ?>
              <input type="hidden" name="trans_import_method" value="file">
              <table class="form-table" role="presentation">
                <tbody>
                <tr>
                  <th><?php esc_html_e('Translation package:', 'wpfanyi-import'); ?></th>
                  <td><label>
                      <input type="file" name="trans_zip"/ accept=".zip">
                    </label></td>
                </tr>
                <tr>
                  <th><?php esc_html_e('Package type:', 'wpfanyi-import'); ?></th>
                  <td><input type="radio" name="trans_type" value="plugin" checked/>
                      <?php esc_html_e('Plugin', 'wpfanyi-import'); ?>
                    <input type="radio" name="trans_type" value="theme"/>
                      <?php esc_html_e('Theme', 'wpfanyi-import'); ?>
                    <input type="radio" name="trans_type" value="auto" class="radio-auto"/>
                      <?php esc_html_e('Auto', 'wpfanyi-import'); ?>
                    <div class="des">
                      <div class="radio-plugin-description description">
                        <p><?php esc_html_e('The standard format of plugin language pack is as follows:', 'wpfanyi-import'); ?></p>
                        <ul class="tree">
                          <li>
                            <div class="treeNode"><i class="dashicons dashicons-before dashicons-media-archive"></i>
                              <span class="title">bbpress-zh_CN.zip</span></div>
                            <ul class="two-level">
                              <li>
                                <div class="treeNode "><i class="dashicons dashicons-before dashicons-media-default"></i>
                                  <span class="title">bbpress-zh_CN.mo</span></div>
                              </li>
                            </ul>
                            <ul class="two-level">
                              <li>
                                <div class="treeNode "><i class="dashicons dashicons-before dashicons-media-default"></i>
                                  <span class="title">bbpress-zh_CN.po</span></div>
                              </li>
                            </ul>
                          </li>
                        </ul>
                      </div>
                      <div class="radio-theme-description description">
                        <p><?php esc_html_e('The standard format of theme language pack is as follows:', 'wpfanyi-import'); ?></p>
                        <ul class="tree">
                          <li>
                            <div class="treeNode"><i class="dashicons dashicons-before dashicons-media-archive"></i>
                              <span class="title">twentytwentyone-zh_CN.zip</span></div>
                            <ul class="two-level">
                              <li>
                                <div class="treeNode "><i class="dashicons dashicons-before dashicons-media-default"></i>
                                  <span class="title">twentytwentyone-zh_CN.mo</span></div>
                              </li>
                            </ul>
                            <ul class="two-level">
                              <li>
                                <div class="treeNode "><i class="dashicons dashicons-before dashicons-media-default"></i>
                                  <span class="title">twentytwentyone-zh_CN.po</span></div>
                              </li>
                            </ul>
                          </li>
                        </ul>
                      </div>
                      <div class="radio-auto-description description">
                        <p><?php esc_html_e('The standard formats of language packages that can auto identify types are as follows:', 'wpfanyi-import'); ?></p>
                        <ul class="tree">
                          <li>
                            <div class="treeNode"><i class="dashicons dashicons-before dashicons-media-archive"></i>
                              <span class="title">translation-zh_CN.zip</span></div>
                            <ul class="two-level">
                              <li>
                                <div class="treeNode "><i class="dashicons dashicons-before dashicons-open-folder"></i>
                                  <span class="title">themes</span></div>
                                <ul class="three-level">
                                  <li>
                                    <div class="treeNode"><i
                                              class="dashicons dashicons-before dashicons-media-default"></i> <span
                                              class="title">twentytwentyone-zh_CN.mo</span></div>
                                  </li>
                                  <li>
                                    <div class="treeNode"><i
                                              class="dashicons dashicons-before dashicons-media-default"></i> <span
                                              class="title">twentytwentyone-zh_CN.po</span></div>
                                  </li>
                                </ul>
                              </li>
                            </ul>
                            <ul class="two-level">
                              <li>
                                <div class="treeNode "><i class="dashicons dashicons-before dashicons-open-folder"></i>
                                  <span class="title">plugins</span></div>
                                <ul class="three-level">
                                  <li>
                                    <div class="treeNode"><i
                                              class="dashicons dashicons-before dashicons-media-default"></i> <span
                                              class="title">bbpress-zh_CN.mo</span></div>
                                  </li>
                                  <li>
                                    <div class="treeNode"><i
                                              class="dashicons dashicons-before dashicons-media-default"></i> <span
                                              class="title">bbpress-zh_CN.po</span></div>
                                  </li>
                                </ul>
                              </li>
                            </ul>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </td>
                </tr>
                </tbody>
              </table>
              <p class="submit">
                <input type="submit" name="submit" class="button-primary"
                       value="<?php esc_html_e('Import', 'wpfanyi-import'); ?>"/>
              </p>
            </form>
          </div>
          <div class="wpfanyi-tab-item">
            <form method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('wpfanyi-import-nonce'); ?>
              <input type="hidden" name="trans_import_method" value="url">
              <table class="form-table" role="presentation">
                <tbody>
                <tr>
                  <th scope="row"><?php esc_html_e('URL address:', 'wpfanyi-import'); ?></th>
                  <td>
                    <div style="display:inline-block;position:relative;">
                      <div class="input_clear">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> ×</button>
                      </div>
                      <input name="trans_url" type="url" id="trans_url" value="" class="trans_url"
                             placeholder="https://wpfanyi.com/glotpress/bulk-export/bbpress/">
                    </div>
                  </td>
                </tr>
                <tr>
                  <th><?php esc_html_e('Package type:', 'wpfanyi-import'); ?></th>
                  <td><input type="radio" name="trans_type" value="plugin" checked/>
                      <?php esc_html_e('Plugin', 'wpfanyi-import'); ?>
                    <input type="radio" name="trans_type" value="theme"/>
                      <?php esc_html_e('Theme', 'wpfanyi-import'); ?>
                    <input type="radio" name="trans_type" value="auto" class="radio-auto"/>
                      <?php esc_html_e('Auto', 'wpfanyi-import'); ?>
                    <div class="des">
                      <div class="radio-plugin-description description">
                        <p><?php esc_html_e('The standard format of theme language pack is as follows:', 'wpfanyi-import'); ?></p>
                        <ul class="tree">
                          <li>
                            <div class="treeNode"><i class="dashicons dashicons-before dashicons-media-archive"></i>
                              <span class="title">bbpress-zh_CN.zip</span></div>
                            <ul class="two-level">
                              <li>
                                <div class="treeNode "><i class="dashicons dashicons-before dashicons-media-default"></i>
                                  <span class="title">bbpress-zh_CN.mo</span></div>
                              </li>
                            </ul>
                            <ul class="two-level">
                              <li>
                                <div class="treeNode "><i class="dashicons dashicons-before dashicons-media-default"></i>
                                  <span class="title">bbpress-zh_CN.po</span></div>
                              </li>
                            </ul>
                          </li>
                        </ul>
                      </div>
                      <div class="radio-theme-description description">
                        <p><?php esc_html_e('The standard format of theme language pack is as follows:', 'wpfanyi-import'); ?></p>
                        <ul class="tree">
                          <li>
                            <div class="treeNode"><i class="dashicons dashicons-before dashicons-media-archive"></i>
                              <span class="title">twentytwentyone-zh_CN.zip</span></div>
                            <ul class="two-level">
                              <li>
                                <div class="treeNode "><i class="dashicons dashicons-before dashicons-media-default"></i>
                                  <span class="title">twentytwentyone-zh_CN.mo</span></div>
                              </li>
                            </ul>
                            <ul class="two-level">
                              <li>
                                <div class="treeNode "><i class="dashicons dashicons-before dashicons-media-default"></i>
                                  <span class="title">twentytwentyone-zh_CN.po</span></div>
                              </li>
                            </ul>
                          </li>
                        </ul>
                      </div>
                      <div class="radio-auto-description description">
                        <p><?php esc_html_e('The standard formats of language packages that can auto identify types are as follows:', 'wpfanyi-import'); ?></p>
                        <ul class="tree">
                          <li>
                            <div class="treeNode"><i class="dashicons dashicons-before dashicons-media-archive"></i>
                              <span class="title">translation-zh_CN.zip</span></div>
                            <ul class="two-level">
                              <li>
                                <div class="treeNode "><i class="dashicons dashicons-before dashicons-open-folder"></i>
                                  <span class="title">themes</span></div>
                                <ul class="three-level">
                                  <li>
                                    <div class="treeNode"><i
                                              class="dashicons dashicons-before dashicons-media-default"></i> <span
                                              class="title">twentytwentyone-zh_CN.mo</span></div>
                                  </li>
                                  <li>
                                    <div class="treeNode"><i
                                              class="dashicons dashicons-before dashicons-media-default"></i> <span
                                              class="title">twentytwentyone-zh_CN.po</span></div>
                                  </li>
                                </ul>
                              </li>
                            </ul>
                            <ul class="two-level">
                              <li>
                                <div class="treeNode "><i class="dashicons dashicons-before dashicons-open-folder"></i>
                                  <span class="title">plugins</span></div>
                                <ul class="three-level">
                                  <li>
                                    <div class="treeNode"><i
                                              class="dashicons dashicons-before dashicons-media-default"></i> <span
                                              class="title">bbpress-zh_CN.mo</span></div>
                                  </li>
                                  <li>
                                    <div class="treeNode"><i
                                              class="dashicons dashicons-before dashicons-media-default"></i> <span
                                              class="title">bbpress-zh_CN.po</span></div>
                                  </li>
                                </ul>
                              </li>
                            </ul>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </td>
                </tr>
                </tbody>
              </table>
              <p class="submit">
                <input type="submit" name="submit" class="button-primary"
                       value="<?php esc_html_e('Import', 'wpfanyi-import'); ?>"/>
              </p>
            </form>
          </div>
        </div>
      </div>
      <div class="wpfanyi-import-config-help stuffbox">
        <button type="button" class="handlediv" aria-expanded="true"><span class="toggle-indicator"
                                                                           aria-hidden="true"></span></button>
        <h2 class="handle">
            <?php esc_html_e('Help', 'wpfanyi-import'); ?>
        </h2>
        <div class="inside">
          <h2>
              <?php esc_html_e('common problem:', 'wpfanyi-import'); ?>
          </h2>
          <ol>
            <li><b>
                    <?php esc_html_e('The installed translation package does not work', 'wpfanyi-import'); ?>
              </b>
              <p>
                  <?php esc_html_e('A:Please check whether the translation package contains a valid .mo file.', 'wpfanyi-import'); ?>
              </p>
              <p>
                  <?php esc_html_e('B: Please check whether you have correctly selected the type of translation package (plugin or theme).', 'wpfanyi-import'); ?>
              </p>
              <p>
                  <?php esc_html_e('C: Please upload by traditional manual method to finally confirm whether the translation package is valid.', 'wpfanyi-import'); ?>
              </p>
            </li>
            <li><b>
                    <?php esc_html_e('Found a bug in this plugin?', 'wpfanyi-import'); ?>
              </b>
                <?php /* translators: %s: https://github.com/WenPai-org/wpfanyi-import/issues */ ?>
              <p><?php printf(__('Please submit issues here: <a href="%s" target="_blank">https://github.com/WenPai-org/wpfanyi-import/issues</a>, we will fix it in the next version, thank you for your feedback!', 'wpfanyi-import'), 'https://github.com/WenPai-org/wpfanyi-import/issues'); ?></p>
            </li>
          </ol>
          <h2>
              <?php esc_html_e('Need to translate a WordPress plugin/theme?', 'wpfanyi-import'); ?>
          </h2>
            <?php /* translators: %s: https://wpfanyi.com/new-project */ ?>
          <p><?php printf(__('If he is hosted in wordpress.org we will handle its translation for free! Please send your needs to: <a href="%s" target="_blank">https://wpfanyi.com/new-project</a>.', 'wpfanyi-import'), 'https://wpfanyi.com/new-project'); ?></p>
        </div>
      </div>
    </div>
  </div>
</div>
