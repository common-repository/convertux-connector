<?php
/** @var bool $isUserLoggedIn */
/** @var null|string $post_data */
?>

<script>
  var covertux_wp = covertux_wp || {};

  covertux_wp.is_user_logged_in = <?php echo is_user_logged_in() ? 'true' : 'false' ?>;

  <?php if($post_data): ?>
    covertux_wp.post_data =<?php echo json_encode($post_data) ?>;
  <?php endif; ?>
</script>

