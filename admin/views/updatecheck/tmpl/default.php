<?php
/**
 * @version 1.9 $Id$
 * @package JEM
 * @copyright (C) 2013-2013 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license GNU/GPL, see LICENSE.php
 
 * JEM is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License 2
 * as published by the Free Software Foundation.
 *
 * JEM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with JEM; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */

defined('_JEXEC') or die;

if ($this->updatedata->failed == 0) {
		?>
		<table style="width:100%" class="adminlist">
			<tr>
		  		<td>
		  		<?php
		  			if ($this->updatedata->current == 0 ) {
		  				echo JHTML::_('image', 'administrator/templates/'. $this->template .'/images/header/icon-48-checkin.png', NULL);
		  			} elseif( $this->updatedata->current == -1 ) {
		  				echo JHTML::_('image', 'administrator/templates/'. $this->template .'/images/header/icon-48-help_header.png', NULL);
		  			} else {
		  				echo JHTML::_('image', 'administrator/templates/'. $this->template .'/images/header/icon-48-help_header.png', NULL);
		  			}
		  		?>
		  		</td>
		  		<td>
		  		<?php
		  			if ($this->updatedata->current == 0) {
		  				echo '<b><font color="green">'.JText::_( 'COM_JEM_LATEST_VERSION' ).'</font></b>';
		  			} elseif( $this->updatedata->current == -1 ) {
		  				echo '<b><font color="red">'.JText::_( 'COM_JEM_OLD_VERSION' ).'</font></b>';
		  			} else {
		  				echo '<b><font color="orange">'.JText::_( 'COM_JEM_NEWER_VERSION' ).'</font></b>';
		  			}
		  		?>
		  		</td>
			</tr>
		</table>

		<br />

		<table style="width:100%" class="adminlist">
			<tr>
		  		<td><b><?php echo JText::_( 'COM_JEM_VERSION' ).':'; ?></b></td>
		  		<td><?php
					echo $this->updatedata->versiondetail;
					?>
		  		</td>
			</tr>
			<tr>
		  		<td><b><?php echo JText::_( 'COM_JEM_RELEASE_DATE' ).':'; ?></b></td>
		  		<td><?php
					echo $this->updatedata->date;
					?>
		  		</td>
			</tr>
			<tr>
		  		<td><b><?php echo JText::_( 'COM_JEM_CHANGES' ).':'; ?></b></td>
		  		<td><ul>
		  			<?php
					foreach ($this->updatedata->changes as $change) {
   						echo '<li>'.$change.'</li>';
					}
					?>
					</ul>
		  		</td>
			</tr>
			<tr>
		  		<td><b><?php echo JText::_( 'COM_JEM_INFORMATION' ).':'; ?></b></td>
		  		<td>
					<a href="<?php echo $this->updatedata->info; ?>" target="_blank">Click for more information</a>
		  		</td>
			</tr>
			<tr>
		  		<td><b><?php echo JText::_( 'COM_JEM_FILES' ).':'; ?></b></td>
		  		<td>
					<a href="<?php echo $this->updatedata->download; ?>" target="_blank">Download upgradepack</a>
		  		</td>
			</tr>
			<tr>
		  		<td><b><?php echo JText::_( 'COM_JEM_NOTES' ).':'; ?></b></td>
		  		<td><?php
					echo $this->updatedata->notes;
					?>
		  		</td>
			</tr>
		</table>

<?php
} else {
?>

		<table style="width:100%" class="adminlist">
			<tr>
		  		<td>
		  		<?php
		  			echo JHTML::_('image', 'administrator/templates/'. $this->template .'/images/header/icon-48-help_header.png', NULL);
		  		?>
		  		</td>
		  		<td>
		  		<?php
		  			echo '<b><font color="red">'.JText::_( 'COM_JEM_CONNECTION_FAILED' ).'</font></b>';
		  		?>
		  		</td>
			</tr>
		</table>
<?php
}
?>

<p class="copyright">
	<?php echo JEMAdmin::footer( ); ?>
</p>