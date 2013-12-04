<?php
class TWP_Devel {
	/**
         * Throw debugging output into Debug My Plugin (3rd party plugin)
         *
         * @param string $panel the panel name, default is 'main'
         * @param string $type the type 'pr' or 'msg'
         * @param string $hdr the message header
         * @param mixed $msg the variable to dump ('pr') or print ('msg')
         * @param string $file __FILE__ from calling location
         * @param string $line __LINE__ from calling location
         * @return null
         */
		public function _render_ToDebugBar( $panel, $type, $hdr, $msg, $file=null, $line=null ) {
			if ( ! isset( $GLOBALS['DebugMyPlugin'] ) ) { return; }

			switch ($type) {
				case 'pr':
					$GLOBALS['DebugMyPlugin']->panels['main']->addPR($hdr,$msg,$file,$line);
					break;

				case 'msg':
					$GLOBALS['DebugMyPlugin']->panels['main']->addMessage($hdr,$msg,$file,$line);
					break;

				default:
					break;
			}
		}
}
