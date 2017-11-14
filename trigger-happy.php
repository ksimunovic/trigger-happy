<?php
/*
Plugin Name: Trigger Happy
Plugin URI: http://hotsource.io/
Description: Connect your plugins and automate your workflow with Trigger Happy - a powerful flow-based visual scripting and automation tool
Version: 1.0.1.4
Text Domain: trigger-happy
*/
// Include class declarations
require_once( dirname( __FILE__ ) . '/src/includes/class-triggerhappy.php' );
require_once( dirname( __FILE__ ) . '/src/includes/class-triggerhappyfield.php' );
require_once( dirname( __FILE__ ) . '/src/includes/class-triggerhappycontext.php' );
require_once( dirname( __FILE__ ) . '/src/includes/class-triggerhappynode.php' );
require_once( dirname( __FILE__ ) . '/src/includes/class-triggerhappyexpressioncompiler.php' );

require_once( dirname( __FILE__ ) . '/src/class-triggerhappy-admin.php' );
require_once( dirname( __FILE__ ) . '/src/class-triggerhappy.php' );
require_once( dirname( __FILE__ ) . '/src/functions.php' );

triggerhappy_initialize();

// Create a helper function for easy SDK access.
function triggerhappy_fs() {
    global $triggerhappy_fs;

    if ( ! isset( $triggerhappy_fs ) ) {
        // Include Freemius SDK.
        require_once dirname(__FILE__) . '/freemius/start.php';

        $triggerhappy_fs = fs_dynamic_init( array(
            'id'                  => '1523',
            'slug'                => 'trigger-happy',
            'type'                => 'plugin',
            'public_key'          => 'pk_e53c382e123211aaf54c6a2233c32',
            'is_premium'          => true,
            // If your plugin is a serviceware, set this option to false.
            'has_premium_version' => true,
            'has_addons'          => false,
            'has_paid_plans'      => true,
            'is_org_compliant'    => false,
            'trial'               => array(
                'days'               => 7,
                'is_require_payment' => true,
            ),
            'menu'                => array(
                'first-path'     => 'plugins.php',
                'contact'        => false,
                'support'        => false,
            ),
            // Set the SDK to work in a sandbox mode (for development & testing).
            // IMPORTANT: MAKE SURE TO REMOVE SECRET KEY BEFORE DEPLOYMENT.
            'secret_key'          => 'sk_k4r_SHw>dbMiv~~4HE4T2QB4aD-UO',
        ) );
    }

    return $triggerhappy_fs;
}
class TH {
    public static function Trigger($triggerName, $setup) {
        $node = TriggerHappy::get_instance()->fetch_node($triggerName);

        $nodeId = 1;
        $args = array();
        foreach ($node['fields'] as $i=>$field) {
            if ($field['dir'] == 'start' || $field['dir'] == 'out') {
                $args[$field['name']] = TH::Expression('_N' . $nodeId . '.' . $field['name']);
            }
        }
        $actions = call_user_func($setup,$args);
        $flow = new TriggerHappyFlow(time(),null,false);
        $trigger = new TriggerHappyNode($nodeId, $triggerName, $flow);

        $nodeId++;

        $prevNode = $trigger;
        foreach ($actions as $i=>$action) {
            $next = array();

            $anode = new TriggerHappyNode($nodeId, $action['type'], $flow);
            $flow->addNode($anode);
            array_push($next,$nodeId);

            $prevNode->setNext($next);
            foreach ($action['args'] as $x=>$arg) {
                $fieldDef = $anode->getFieldDef($x);

                $afield = $anode->getField($x);
                $afield->setExpression($arg);

            }


        }
        $flow->addNode( $trigger );
        $flow->start();
    }
    public static function Filter($left, $op, $right) {
        return array(
            'left'=>array('expr'=>$left),
            'op'=>$op,
            'right'=>array('expr'=>$right)
        );
    }
    public static function Graph() {
        $nodes = array();
        $prevNode = null;
        foreach (func_get_args() as $i=>$node) {

            $node['nid'] = $i+1;
            if ($i > 0)
                $prevNode = $nodes[$i-1];
            if ( $prevNode != null && !isset($prevNode['next'])) {
                $nodes[$i-1]['next'] = array($i+1);

            }

            array_push($nodes,$node);

            $i++;
        }
        return array(
            array(

                'nodes' =>
                $nodes
            )
        );
    }
    public static function Expression($expr) {
        $phep = new PHPEP($expr);
        return $phep->exec();
    }
    public static function Action($actionName, $args) {
        $node = TriggerHappy::get_instance()->fetch_node($actionName);
        return array(
            'type'=>$actionName,
            'args'=>$args,
            'node'=>$node
        );
    }


}
