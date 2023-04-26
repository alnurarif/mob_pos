<?php 
foreach ($messages as $message): 
  $user = $this->ion_auth->user($message->user_id)->row();
  if ($user): 
  
  $logged_in = $this->ion_auth->get_user_id();
  $owner = false;
  if ($message->user_id === $logged_in) {
    $owner = true;
  }
?>
  <!-- Message. Default to the left -->
  <div class="direct-chat-msg <?php echo $owner ? 'right' :''; ?>">
    <div class="direct-chat-info clearfix">
      <span class="direct-chat-name <?php echo $owner ? 'pull-right' :'pull-left'; ?>"><?php echo escapeStr($user->first_name); ?> <?php echo escapeStr($user->last_name); ?></span>
      <span class="direct-chat-timestamp <?php echo $owner ? 'pull-left' :'pull-right'; ?>"><?php echo $this->repairer->time_elapsed_string($message->timestamp); ?>
         <i id="edit_chat" data-num="<?=$message->id;?>" class="fa fa-edit"></i> 
        <i id="delete_chat" data-num="<?=$message->id;?>" class="fa fa-trash"></i>
      </span>
    </div>
    <!-- /.direct-chat-info -->
    <img class="direct-chat-img" src="<?php echo base_url(); ?>assets/uploads/members/<?php echo $user->image; ?>" alt="Message User Image"><!-- /.direct-chat-img -->
    <div class="direct-chat-text">
      <?php echo escapeStr($message->message); ?>
    </div>
    <!-- /.direct-chat-text -->
  </div>
  <!-- /.direct-chat-msg -->
<?php endif; endforeach; ?>