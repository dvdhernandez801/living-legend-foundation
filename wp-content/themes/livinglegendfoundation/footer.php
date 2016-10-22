    <div class="art-footer">
                <div class="art-footer-body">
                <?php get_sidebar('footer'); ?>
                    <a href="<?php bloginfo('rss2_url'); ?>" class='art-rss-tag-icon' title="<?php printf(__('%s RSS Feed', THEME_NS), get_bloginfo('name')); ?>"></a>
                            <div class="art-footer-text">
                                <?php  echo do_shortcode(theme_get_option('theme_footer_content')); ?>
                            </div>
                    <div class="cleared"></div>
                </div>
            </div>
    		<div class="cleared"></div>
        </div>
    </div>
    <div class="cleared"></div>
    <p class="art-page-footer">Designed by <a href="http://www.businessintheblack.com" target="_blank">Business In The Black</a>.</p>
    <div class="cleared"></div>
</div>
    <div id="wp-footer">
	        <?php wp_footer(); ?>
	        <!-- <?php printf(__('%d queries. %s seconds.', THEME_NS), get_num_queries(), timer_stop(0, 3)); ?> -->
    </div>
</body>
</html>

