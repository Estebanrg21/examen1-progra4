<?php if(isset($popSuccessModal) || isset($popErrorModal)) : ?>
    <script>
      window.history.replaceState({}, document.title, `${window.location.pathname}`);
    </script>
    <div class="modal fade <?php echo $classModal?>-modal-container" id="<?php echo $classModal?>Modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="<?php echo $classModal?>ModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down position-relative">
        <div class="px-3  modal-content <?php echo $classModal?>-modal d-flex flex-column justify-content-around" >
        <button type="button" class="btn-close position-absolute top-2 m-0 p-0 end-4" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="<?php echo $classModal?>-modal-animation">
            <script src="/assets/js/lottie-player.js"></script>
            <?php if(isset($popSuccessModal)): ?>
              <lottie-player src="/assets/lottie-animations/lf20_bkizmjpn.json"  background="transparent"  speed="1"  style="width: 300px; height: 300px;"    autoplay></lottie-player>
            <?php else : ?>
              <lottie-player src="/assets/lottie-animations/lf20_46u4ucum.json"  background="transparent"  speed="1"  style="width: 300px; height: 300px;"  autoplay></lottie-player>
            <?php endif; ?>
          </div>
          <h3 class="mt-4 text-center text-white text-break"> <?php echo (isset($popSuccessModal)?$successMessage:$errorMessage) ?></h3>
          
          <button type="button" data-bs-dismiss="modal" class="btn btn-outline-<?php echo $classModal?> w-50 center align-self-center modal-button-confirm">Entendido!</button>
        </div>
      </div>
    </div>
  <?php endif; ?>