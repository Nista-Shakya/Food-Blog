<p><?php _e('You need the Premium version of <b>WP Frontend Admin</b> to view this admin page on the frontend.', VG_Admin_To_Frontend::$textname); ?></p>
<p><?php _e(apply_filters('vg_admin_to_frontend/wrong_plan/free_version_features_text', 'The free version only lets you display blog pages on the frontend (posts list, post editor, post tags and categories)'), VG_Admin_To_Frontend::$textname); ?></p>

<p><a href="<?php echo esc_url(VG_Admin_To_Frontend_Obj()->args['buy_link']); ?>"><?php echo sanitize_text_field(VG_Admin_To_Frontend_Obj()->args['buy_link_text']); ?></a> - or - <a href="javascript:void(0)" class="vg-frontend-admin-show-tutorial" onclick="document.getElementsByClassName('vg-frontend-admin-tutorial')[0].style.display = 'block'; return false;"><?php _e('View demo', VG_Admin_To_Frontend::$textname); ?></a></p>
<iframe style="display: none;" class="vg-frontend-admin-tutorial" width="560" height="315" src="https://www.youtube.com/embed/EG1NE3X5yNs?rel=0&amp;controls=0&amp;showinfo=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>