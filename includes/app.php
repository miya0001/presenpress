<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8">

        <title><?php the_title(); ?></title>

        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />

        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

        <?php wp_head(); ?>
    </head>

    <body>

        <div class="reveal">

            <div class="slides">
                <?php while(have_posts()): the_post(); ?>
                    <?php global $numpages; ?>
                    <?php if ($numpages > 1): ?>
                        <?php for ($i = 1; $i <= $numpages; $i++ ): ?>
                            <section>
                                <?php global $page; $page = $i; ?>
                                <?php echo apply_filters('presenpress_content', get_the_content()); ?>
                            </section>
                        <?php endfor; ?>
                    <?php else: ?>
                        <?php echo wpautop(do_shortcode(get_the_content())); ?>
                    <?php endif; ?>
                <?php endwhile; ?>
            </div>

        </div>

        <?php wp_footer(); ?>

        <div id="presenpress-cursor"></div>
        <div id="presenpress-highlight"></div>
    </body>
</html>
