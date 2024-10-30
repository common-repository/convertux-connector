<?php
/** @var bool $success */
/** @var string $key */
/** @var string $nonce */
?>

<div class="">
  <main class="cta-main cta-container">
    <div class="cta-auth-heading">
      <div class="heading-logo">
        <img src="<?php echo plugins_url('images/logo-dark.svg', __FILE__); ?>">
      </div>
    </div>

      <?php if ($success === true): ?>
        <div class="">Convertux key successfully saved!</div>
      <?php endif; ?>
      <?php if ($success === false): ?>
        <div class="g-error">Incorrect convertux key!</div>
      <?php endif; ?>

    <form class="cta-auth-form" role="form" method="POST" action="">
      <div class="cta-form-group">
        <div class="cta-form-control">
          <input
            class="cta-inp"
            type="text"
            name="convertux_key"
            placeholder="Place your convertux key"
            value="<?php echo esc_attr($key) ?>"
          >
          <input
            type="hidden"
            name="nonce"
            value="<?php echo esc_attr($nonce) ?>"
          >
        </div>
      </div>
      <div class="cta-form-group">
        <div class="cta-btn-group">
          <button class="cta-btn cta-btn--block" type="submit" id="signInBtn">
            <span>Submit</span>
          </button>
        </div>
      </div>
    </form>
  </main>
</div>
