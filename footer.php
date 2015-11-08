<?php
/**
 * The template for displaying the footer
 *
 * @author Javis <javismay@gmail.com>
 * @license MIT
 * @since 1.0
 * @version 1.0
 */
?>
		</div>

		<footer id="footer" <?php if(lo_opt('index_bg')) echo 'style="background-image: url(\''.lo_opt('index_bg').'\');"';?>role="contentinfo" itemscope itemtype="http://schema.org/WPFooter">
			<div class="footer-wrap">
				<div class="information"><p><?php echo lo_opt('footer_info');?></p></div>
				<div class="copyright"><p>Proudly powered by <a href="https://wordpress.org/" target="_blank">WordPress</a> · Theme by <a href="http://lostg.com" target="_blank">Javis</a></p></div>
			</div>
		</footer>
	</div>
	<a id="gotop" href="javascript:;" class="iconfont icon-up"></a>

<?php 
wp_footer();
if( lo_opt('footer_js') != '' ) echo '<script>'.lo_opt('footer_js').'</script>';
if( lo_opt('tongji_js') != '' ) echo '<script>'.lo_opt('tongji_js').'</script>';
?>
</body>
</html>