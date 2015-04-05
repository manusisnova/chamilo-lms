<?php
/* For licensing terms, see /license.txt */

/**
 * Hook to limit session resubscriptions
 *
 * @author Imanol Losada Oriol <imanol.losada@beeznest.com>
 * @package chamilo.plugin.resubscription
 */
class HookResubscription extends HookObserver implements HookResubscribeObserverInterface
{

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct(
            'plugin/resubscription/src/Resubscription.php', 'resubscription'
        );
    }

    /**
     * Limit session resubscription when a Chamilo user is resubscribed to a session
     * @param HookCreateUserEventInterface $hook The hook
     */
    public function hookResubscribe(HookResubscribeEventInterface $hook)
    {
        $data = $hook->getEventData();
        if ($data['type'] === HOOK_EVENT_TYPE_PRE) {

            $resubscriptionLimit = Resubscription::create()->get('resubscription_limit');

            $limitDate = gmdate('Y-m-d');

            switch ($resubscriptionLimit) {
                case 'calendar_year':
                    $resubscriptionLimit = "1 year";
                    $limitDate = gmdate('Y-m-d', strtotime(gmdate('Y-m-d')." -$resubscriptionLimit"));
                    break;
            }

            $join = " INNER JOIN ".Database::get_main_table(TABLE_MAIN_SESSION)."ON id = id_session";

            // User sessions and courses
            $userSessions = Database::select(
                'id_session, date_end',
                Database::get_main_table(TABLE_MAIN_SESSION_USER).$join,
                array(
                    'where' => array(
                        'id_user = ? AND date_end >= ?' => array(
                            api_get_user_id(),
                            $limitDate
                        )
                    ),
                    'order' => 'date_end DESC'
                )
            );
            $userSessionCourses = array();
            foreach ($userSessions as $userSession) {
                $userSessionCourseResult = Database::select(
                    'course_code',
                    Database::get_main_table(TABLE_MAIN_SESSION_COURSE),
                    array(
                        'where' => array(
                            'id_session = ?' => array(
                                $userSession['id_session']
                            )
                        )
                    )
                );
                foreach ($userSessionCourseResult as $userSessionCourse) {
                    if (!isset($userSessionCourses[$userSessionCourse['course_code']])) {
                        $userSessionCourses[$userSessionCourse['course_code']] = $userSession['date_end'];
                    }

                }
            }

            // Current session and courses
            $currentSessionCourseResult = Database::select(
                'course_code',
                Database::get_main_table(TABLE_MAIN_SESSION_COURSE),
                array(
                    'where' => array(
                        'id_session = ?' => array(
                            $data['session_id']
                        )
                    )
                )
            );

            // Check if current course code matches with one of the users
            foreach ($currentSessionCourseResult as $currentSessionCourse) {
                if (isset($userSessionCourses[$currentSessionCourse['course_code']])) {
                    $endDate = $userSessionCourses[$currentSessionCourse['course_code']];
                    $resubscriptionDate = gmdate('Y-m-d', strtotime($endDate." +$resubscriptionLimit"));
                    $icon = Display::return_icon('students.gif', get_lang('Student'));
                    $canResubscribeFrom = sprintf(get_plugin_lang('CanResubscribeFromX', 'resubscription'), $resubscriptionDate);
                    throw new Exception(Display::label($icon . ' ' . $canResubscribeFrom, "info"));
                }
            }
        }
    }
}