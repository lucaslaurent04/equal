<?php
/*
    This file is part of the eQual framework <http://www.github.com/equalframework/equal>
    Some Rights Reserved, Cedric Francoys, 2010-2024
    Licensed under GNU GPL 3 license <http://www.gnu.org/licenses/>
*/
namespace core\security;

use equal\orm\Model;

class SecurityPolicyRule extends Model {

    public static function getName() {
        return 'Security Policy Rule';
    }

    public static function getDescription() {
        return "A Security Policy Rule allow to check a Request against a specific validation.";
    }

    public static function getColumns() {
        return [

            'user_id' => [
                'type'              => 'many2one',
                'foreign_object'    => 'core\User',
                'description'       => 'User the rule is specific to (optional).',
                'default'           => 0,
                'ondelete'          => 'cascade'
            ],

            'security_policy_id' => [
                'type'              => 'many2one',
                'foreign_object'    => 'core\security\SecurityPolicy',
                'description'       => 'Security policy the rule relates to.'
            ],

            'policy_rule_type' =>  [
                'type'              => 'string',
                'usage'             => 'text/plain:10',
                'selection'         => [
                    'ip_address',
                    'location',
                    'user_group',
                    'user_login',
                    'time_range'
                ],
                'dependents'        => ['description'],
                'description'       => 'Type of rule (kind of test to perform).'
            ],

            'description' => [
                'type'              => 'computed',
                'result_type'       => 'string',
                'usage'             => 'text/plain',
                'description'       => "Description of the role and tests performed by the policy.",
                'function'          => 'calcDescription',
                'store'             => true
            ],

            'rule_values_ids' => [
                'type'              => 'one2many',
                'foreign_object'    => 'core\security\SecurityPolicyRuleValue',
                'foreign_field'     => 'policy_rule_id',
                'description'       => 'List of values to be tested for the policy rules.'
            ]

        ];
    }

    public static function calcDescription($self) {
        $result = [];
        $self->read(['policy_rule_type']);

        foreach($self as $id => $rule) {
            switch($rule['policy_rule_type']) {
                case 'ip_address':
                    $result[$id] = 'Request IP address match against one or more values.';
                    break;
                case 'location':
                    $result[$id] = 'Request geo-location matching one value against a set of cities or regions.';
                    break;
                case 'user_group':
                    $result[$id] = 'User belonging to at least one of the listed groups.';
                    break;
                case 'user_login':
                    $result[$id] = 'User login (email) matching a given pattern.';
                    break;
                case 'time_range':
                    $result[$id] = 'Time of Request included in at least one the listed time ranges.';
                    break;
            }
        }
        return $result;
    }

}