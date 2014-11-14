<?php
/**
 * @file
 * Hooks provided by Login Security module.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Alter the login attempts message and display flag before output.
 *
 * @param string $message_raw
 *   The related login attempts message that will be displayed.
 *
 * @param bool $display_block_attempts
 *   A flag to set whether the message will be displayed or not.
 *
 * @param int $current_attempts_count
 *   A simple context information to inform the current count of the attempts.
 */
function hook_login_security_display_block_attempts_alter(&$message_raw, &$display_block_attempts, $current_attempts_count) {
}

/**
 * @} End of "addtogroup hooks".
 */