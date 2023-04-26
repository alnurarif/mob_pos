<div class="wrapper">
		<?php $this->load->view($this->theme . '_partials/navigation'); ?>

	<?php // Left side column. contains the logo and sidebar ?>
	<aside class="main-sidebar">
		<section class="sidebar">
			  <div class="user-panel">
		        <div class="pull-left image">
		          <img src="<?php echo base_url(); ?>assets/uploads/members/<?php echo $user->image; ?>" class="img-circle" alt="User Image">
		        </div>
		        <div class="pull-left info">
		          <p><?php echo $user->first_name.' '.$user->last_name; ?></p>
		          <a href="#"><i class="fas fa-circle text-success"></i> Online</a>
		        </div>
		      </div>
			<?php // (Optional) Add Search box here ?>
			<?php $this->load->view($this->theme . '_partials/sidemenu'); ?>
		</section>
	</aside>

	<?php // Right side column. Contains the navbar and content of the page ?>
	<div class="content-wrapper" id="content-wrapper">
		<?php if($show_page_title): ?>
			<section class="content-header">
				<h1><?php echo $page_title; ?></h1>
			</section>
		<?php endif;?>
		<section class="content">
			<?php if (isset($_SESSION['message'])) { ?>
		      <div class="alert alert-success">
		          <button data-dismiss="alert" class="close" type="button">×</button>
		          <?php echo $_SESSION['message']; ?>
		      </div>
		  <?php } ?>
		  <?php if (isset($_SESSION['error'])) { ?>
		      <div class="alert alert-danger">
		          <button data-dismiss="alert" class="close" type="button">×</button>
		          <?php echo ($_SESSION['error']); ?>
		      </div>
		  <?php } ?>
		  <?php if (isset($_SESSION['warning'])) { ?>
		      <div class="alert alert-warning">
		          <button data-dismiss="alert" class="close" type="button">×</button>
		          <?php echo ($_SESSION['warning']); ?>
		      </div>
		  <?php } ?>

			<?php $this->load->view($this->theme . $inner_view); ?>
		<div class="clearfix"></div>
			
		</section>
		<div class="clearfix"></div>
	</div>

	<?php // Footer ?>
	<?php $this->load->view($this->theme . '_partials/footer'); ?>
	<?php $this->load->view($theme.'client_js'); ?>
</div>