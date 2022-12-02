<?php

namespace Elementor;

class post_grid_carousel extends Widget_Base
{

  public function get_name()
  {
    return 'post_grid_carousel';
  }

  public function get_title()
  {
    return __('Post Carousel');
  }

  public function get_icon()
  {
    return 'eicon-slides';
  }

  public function __construct($data = [], $args = null)
  {
    parent::__construct($data, $args);
    wp_enqueue_style( 'post-grid-carousel', plugin_dir_url( __DIR__  ) . '/css/post-grid-carousel.css','1.1.0');
  }

   // public function get_style_depends() {
   //  wp_register_style( 'post-grid-carousel', plugin_dir_url( __DIR__  ) . '/css/post-grid-carousel.css','1.1.0');
   //   return [ 'post-grid-carousel' ];
   // }



  public function get_categories()
  {
    return ['general'];
  }

  protected function _register_controls(){
    $mine = array();
    $categories = get_categories(array(
      'orderby'   => 'name',
      'order'     => 'ASC'
    ));

    foreach ($categories as $category ) {
       $mine[$category->term_id] = $category->name;
    }

    $this->start_controls_section(
      'content_section',
      [
        'label' => __( 'Content', 'post-plus' ),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    // Post categories.
    $this->add_control(
      'category',
      [
        'label' => '<i class="fa fa-folder"></i> ' . __( 'Category', 'yp-core' ),
        'type' => \Elementor\Controls_Manager::SELECT2,
        'default' => 'none',
        'options'   => $mine,
        'multiple' => false,
      ]
    );

    $this->add_control(
        'per_posts',
        [
          'label' => __( 'Posts Per Page', 'yp-core' ),
          'type' => \Elementor\Controls_Manager::TEXT,
          'default' => __( '5', 'yp-core' ),
          'placeholder' => __( 'เช่น 5', 'yp-core' ),
        ]
      );

      $this->add_responsive_control(
        'column',
        [
          'type' => \Elementor\Controls_Manager::TEXT,
          'label' => esc_html__( 'Column', 'yp-core' ),

          'devices' => [ 'desktop', 'tablet', 'mobile' ],
          'desktop_default' => 1,
          'tablet_default' => 3,
          'mobile_default' => 2,
        ]
      );
      $this->add_responsive_control(
    'space_between',
    [
      'type' => \Elementor\Controls_Manager::TEXT,
      'label' => esc_html__( 'Spacing', 'yp-core' ),
      // 'range' => [
      // 	'px' => [
      // 		'min' => 0,
      // 		'max' => 100,
      // 	],
      // ],
      'devices' => [ 'desktop', 'tablet', 'mobile' ],
      'desktop_default' => 10,
      'tablet_default' => 10,
      'mobile_default' => 10,
      // 'selectors' => [
      // 	'{{WRAPPER}} .widget-image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
      // ],
    ]
  );

      $this->add_control(
          'post_offset',
          [
            'label' => __( 'Offset', 'yp-core' ),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => __( '', 'yp-core' ),
            'placeholder' => __( 'เช่น 1', 'yp-core' ),
          ]
        );

  }

  protected function render()
  {
    $settings = $this->get_settings_for_display();
    $pagination_number = $settings['pagination_number'];
    $column_desktop = $settings['column'];
    $column_tablet = $settings['column_tablet'];
    $column_mobile = $settings['column_mobile'];
    $widget_id = $this->get_id();

    $space_between = $settings['space_between'];
    if ($space_between == '') {
      $space_between = 10;
    }


    $space_between_tablet = $settings['space_between_tablet'];
    $space_between_mobile = $settings['space_between_mobile'];
    ob_start();
?>
    <?php if ($_GET['action'] == '') : ?>
      <?php
      $args = array(
      'post_type' => array( 'post'),
      'tax_query'         => array(
             array(
                 'taxonomy'  => 'category',
                 'field'     => 'term_id',
                 'terms'     => $settings['category']
             )
           ),
      'posts_per_page'  => $settings['per_posts'],
      'offset'    => $offset,
      'orderby'    => 'ID',
      'order'    => 'DESC'
      );
      query_posts( $args );
      ?>
      <?php if ( have_posts()) : ?>
    <div class="box-carousel-post">
      <div class="swiper post_grid_carousel vc_post id-<?php echo $widget_id; ?>">

        <div class="swiper-wrapper">

            <?php while ( have_posts() ) : the_post(); ?>
            <div class="swiper-slide">
                <?php get_template_part( 'template-parts/content', 'card-post');  ?>
            </div>

            <?php endwhile; ?>
            <?php wp_reset_query(); ?>


        </div>
        <!-- <div class="swiper-pagination"></div> -->

      </div>

      <div class="post_grid_carousel_nav id-<?php echo $widget_id; ?>">
        <div class="swiper-button-prev">
          <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
            <polyline points="15 18 9 12 15 6"></polyline>
          </svg>
        </div>
        <div class="swiper-button-next">
          <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </div>
      </div>




      <script type="module">
      var swiper = new Swiper(".post_grid_carousel.id-<?php echo $widget_id; ?>", {
        slidesPerView: <?php echo $settings['column']; ?>,
        spaceBetween: <?php echo $space_between; ?>,
        loop: true,
        // pagination: {
        //   el: ".swiper-pagination",
        //   clickable: true,
        // },
        autoHeight: true,
        breakpoints: {
           320: {
             slidesPerView: <?php echo $column_mobile; ?>,
             spaceBetween: <?php echo $space_between_mobile; ?>,
           },
           768: {
             slidesPerView: <?php echo $column_tablet; ?>,
             spaceBetween: <?php echo $space_between_tablet; ?>,
           },
           1024: {
             slidesPerView: <?php echo $column_desktop; ?>,
             spaceBetween: <?php echo $space_between; ?>,
           },
         },
        navigation: {
          nextEl: ".post_grid_carousel_nav.id-<?php echo $widget_id; ?> .swiper-button-next",
          prevEl: ".post_grid_carousel_nav.id-<?php echo $widget_id; ?> .swiper-button-prev",
        },
      });


      </script>
       </div>
    <?php else : ?>
      <img src="<?php echo plugin_dir_url(__DIR__) . 'image/post-preview.jpg'; ?>" alt="b1-01">
    <?php endif; ?>

  <?php endif; ?>

  <?php

    $output_string = ob_get_contents();
    ob_end_clean();
    echo $output_string;
  }

  protected function _content_template()
  {
    ob_start();
  ?>
    <img src="<?php echo plugin_dir_url(__DIR__) . 'image/post-preview.jpg'; ?>" alt="b1-01">
<?php
    $output_string = ob_get_contents();
    ob_end_clean();
    echo $output_string;
  }
}
