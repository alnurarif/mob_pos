<ul class="sidebar-menu" data-widget="tree">
  
    <li class="header"><?php echo lang('main_nav_span');?></li>
    <?php foreach ($menu as $parent => $parent_params): ?>
        <?php
            $parent_array = explode(',', $parent);
        ?>
        <?php if ( empty($page_auth[$parent_params['url']]) || $this->ion_auth->in_group($page_auth[$parent_params['url']]) ): ?>
            <?php if ( empty($parent_params['children']) ): ?>
                <?php if($this->Admin || (@$this->GP[str_replace('/', '-', $parent_params['name'])] || $parent_params['name'] == 'welcome/index' )): ?>
                    <?php $active = ($current_uri==$parent_params['url'] || in_array($ctrler, $parent_array)); ?>
                    <li class='<?php if ($active) echo 'active'; ?>'>
                      <a href='<?php echo base_url();?>panel/<?php echo $parent_params['url']; ?>'>
                        <i class='<?php echo $parent_params['icon']; ?>'></i> 
                        <span><?php echo lang($parent_params['name']); ?></span>
                      </a>
                    </li>
                <?php endif; ?>
            <?php else: ?>
                <?php $parent_active = in_array($ctrler, $parent_array); ?>
                 <li class="treeview <?php if ($parent_active) echo 'active'; ?>">
                  <a href="#">
                    <i class="<?php echo $parent_params['icon']; ?>"></i> <span><?php echo lang($parent_params['name']); ?></span>
                    <span class="pull-right-container">
                      <i class="fas fa-angle-left pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    
                    <?php foreach ($parent_params['children'] as $child): ?>
                        <?php if($this->Admin || (!array_key_exists(str_replace('/', '-', $child['name']), $this->GP) || @$this->GP[str_replace('/', '-', $child['name'])])): ?>
                            <?php $child_active = ($current_uri=='panel/'.$child['url']); ?>
                            <li <?php if ($child_active) echo 'class="active"'; ?>><a href="<?php echo base_url();?>panel/<?php echo $child['url']; ?>"><i class="far fa-circle"></i> <?php echo lang($child['name']); ?></a></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                  </ul>
                </li>
            <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>
<!-- /.sidebar -->
</aside>