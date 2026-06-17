<?php

if ( ! class_exists( 'EyeOnChatbot' ) ) {
	class EyeOnChatbot {

		private $mcd_settings;
		private $enabled = false;

		function __construct() {
			$this->mcd_settings = get_option( MCD_REDUX_OPT_NAME );
		}

		function register() {
			add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue' ) );
			add_action( 'wp_footer', array( $this, 'render' ) );

			add_action( 'wp_ajax_eyeon_chat_request', array( $this, 'chat_request' ) );
			add_action( 'wp_ajax_nopriv_eyeon_chat_request', array( $this, 'chat_request' ) );
		}

		function is_enabled() {
			if ( $this->enabled ) {
				return true;
			}
			$this->enabled = function_exists( 'eyeon_is_chatbot_enabled' ) && eyeon_is_chatbot_enabled();
			return $this->enabled;
		}

		function maybe_enqueue() {
			if ( is_admin() || ! $this->is_enabled() ) {
				return;
			}

			mcd_include_css( 'chatbot', 'assets/chatbot/chatbot.css' );
			mcd_include_js( 'chatbot', 'assets/chatbot/chatbot.js', true );

			$accent = ! empty( $this->mcd_settings['accent_color'] ) ? $this->mcd_settings['accent_color'] : '#3d80b9';

			wp_localize_script(
				'eyeon-chatbot',
				'EYEON_CHATBOT',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'nonce' => wp_create_nonce( 'eyeon_api_nonce' ),
					'botName' => ! empty( $this->mcd_settings['chatbot_bot_name'] ) ? $this->mcd_settings['chatbot_bot_name'] : 'Center Assistant',
					'welcomeMessage' => ! empty( $this->mcd_settings['chatbot_welcome_message'] ) ? $this->mcd_settings['chatbot_welcome_message'] : 'Hi! Ask me anything about our center.',
					'offlineMessage' => ! empty( $this->mcd_settings['chatbot_offline_message'] ) ? $this->mcd_settings['chatbot_offline_message'] : 'Sorry, the assistant is temporarily unavailable.',
					'position' => ! empty( $this->mcd_settings['chatbot_position'] ) ? $this->mcd_settings['chatbot_position'] : 'bottom-right',
					'iconUrl' => ! empty( $this->mcd_settings['chatbot_icon_url'] ) ? $this->mcd_settings['chatbot_icon_url'] : '',
					'accentColor' => $accent,
					'linkBases' => array(
						'deal' => mcd_single_page_url( 'mycenterdeal' ),
						'store' => mcd_single_page_url( 'mycenterstore' ),
						'event' => mcd_single_page_url( 'mycenterevent' ),
						'career' => mcd_single_page_url( 'mycentercareer' ),
						'news' => mcd_single_page_url( 'mycenterblogpost' ),
					),
				)
			);
		}

		function render() {
			if ( is_admin() || ! $this->is_enabled() ) {
				return;
			}

			$position = ! empty( $this->mcd_settings['chatbot_position'] ) ? $this->mcd_settings['chatbot_position'] : 'bottom-right';
			$bot_name = ! empty( $this->mcd_settings['chatbot_bot_name'] ) ? $this->mcd_settings['chatbot_bot_name'] : 'Center Assistant';
			$icon_url = ! empty( $this->mcd_settings['chatbot_icon_url'] ) ? $this->mcd_settings['chatbot_icon_url'] : '';
			$accent = ! empty( $this->mcd_settings['accent_color'] ) ? $this->mcd_settings['accent_color'] : '#3d80b9';
			?>
			<div id="eyeon-chatbot-root" class="eyeon-chatbot eyeon-chatbot--<?php echo esc_attr( $position ); ?>" style="--eyeon-chat-accent: <?php echo esc_attr( $accent ); ?>;" aria-live="polite">
				<button type="button" class="eyeon-chatbot__launcher" id="eyeon-chatbot-launcher" style="background-color: <?php echo esc_attr( $accent ); ?>;" aria-label="<?php echo esc_attr( sprintf( __( 'Open %s', EYEON_NAMESPACE ), $bot_name ) ); ?>">
					<?php if ( $icon_url ) : ?>
						<img src="<?php echo esc_url( $icon_url ); ?>" alt="" class="eyeon-chatbot__launcher-icon eyeon-chatbot__launcher-icon--image" />
					<?php else : ?>
						<svg class="eyeon-chatbot__launcher-icon eyeon-chatbot__launcher-icon--svg" viewBox="0 0 24 24" aria-hidden="true"><path fill="#ffffff" d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H5.17L4 17.17V4h16v12z"/></svg>
					<?php endif; ?>
				</button>
				<div class="eyeon-chatbot__panel" id="eyeon-chatbot-panel" hidden>
					<div class="eyeon-chatbot__header">
						<div class="eyeon-chatbot__header-info">
							<?php if ( $icon_url ) : ?>
								<img src="<?php echo esc_url( $icon_url ); ?>" alt="" class="eyeon-chatbot__avatar" />
							<?php endif; ?>
							<strong class="eyeon-chatbot__title"><?php echo esc_html( $bot_name ); ?></strong>
						</div>
						<button type="button" class="eyeon-chatbot__close" id="eyeon-chatbot-close" aria-label="<?php esc_attr_e( 'Close chat', EYEON_NAMESPACE ); ?>">&times;</button>
					</div>
					<div class="eyeon-chatbot__messages" id="eyeon-chatbot-messages"></div>
					<form class="eyeon-chatbot__form" id="eyeon-chatbot-form">
						<input type="text" class="eyeon-chatbot__input" id="eyeon-chatbot-input" placeholder="<?php esc_attr_e( 'Type your question...', EYEON_NAMESPACE ); ?>" maxlength="500" autocomplete="off" />
						<button type="submit" class="eyeon-chatbot__send" id="eyeon-chatbot-send"><?php esc_html_e( 'Send', EYEON_NAMESPACE ); ?></button>
					</form>
				</div>
			</div>
			<?php
		}

		function chat_request() {
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
			if ( ! wp_verify_nonce( $nonce, 'eyeon_api_nonce' ) ) {
				wp_send_json_error( array( 'msg' => "You're not authorized to make this request." ), 403 );
			}

			if ( ! eyeon_is_chatbot_enabled() ) {
				wp_send_json_error( array( 'msg' => 'Chatbot is not enabled for this center.' ), 403 );
			}

			$message = isset( $_POST['message'] ) ? sanitize_text_field( wp_unslash( $_POST['message'] ) ) : '';
			if ( empty( $message ) ) {
				wp_send_json_error( array( 'msg' => 'Message is required.' ), 400 );
			}

			$history = array();
			if ( ! empty( $_POST['history_json'] ) ) {
				$decoded = json_decode( wp_unslash( $_POST['history_json'] ), true );
				if ( is_array( $decoded ) ) {
					$_POST['history'] = $decoded;
				}
			}
			if ( ! empty( $_POST['history'] ) && is_array( $_POST['history'] ) ) {
				foreach ( array_slice( $_POST['history'], -6 ) as $item ) {
					if ( ! is_array( $item ) ) {
						continue;
					}
					$role = isset( $item['role'] ) ? sanitize_text_field( $item['role'] ) : '';
					$content = isset( $item['content'] ) ? sanitize_textarea_field( wp_unslash( $item['content'] ) ) : '';
					if ( in_array( $role, array( 'user', 'assistant' ), true ) && $content !== '' ) {
						$history[] = array(
							'role' => $role,
							'content' => mb_substr( $content, 0, 2000 ),
						);
					}
				}
			}

			$result = mcd_api_post(
				MCD_API_CHAT,
				array(
					'message' => mb_substr( $message, 0, 500 ),
					'history' => $history,
				)
			);

			if ( $result['status'] === 200 && ! empty( $result['data']['reply'] ) ) {
				wp_send_json_success( $result['data'] );
			}

			$error_msg = 'Unable to get a response. Please try again.';
			if ( ! empty( $result['data']['error']['description'] ) ) {
				$desc = $result['data']['error']['description'];
				if ( is_array( $desc ) ) {
					$error_msg = implode( ' ', array_map( 'strval', $desc ) );
				} else {
					$error_msg = (string) $desc;
				}
			} elseif ( ! empty( $result['data']['error']['error_message'] ) ) {
				$error_msg = 'AI service error. Please try again later.';
			}

			wp_send_json_error(
				array(
					'msg' => $error_msg,
					'status' => $result['status'],
				),
				$result['status'] >= 400 ? $result['status'] : 500
			);
		}
	}

	$eyeonChatbot = new EyeOnChatbot();
	$eyeonChatbot->register();
}
