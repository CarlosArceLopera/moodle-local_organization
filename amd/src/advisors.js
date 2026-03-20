import notification from 'core/notification';
import ajax from 'core/ajax';
import {get_string as getString} from 'core/str';

export const init = () => {
    deleteAdvisor();
};

/**
 * Delete advisor
 */
function deleteAdvisor() {
    // Pop-up notification when .btn-local-organization-delete-advisor is clicked
    document.querySelectorAll('.btn-local-organization-delete-advisor').forEach(button => {
        button.addEventListener('click', function () {
            // Get the data id attribute value
            var id = this.getAttribute('data-id');
            var role_id = this.getAttribute('data-role_id');
            var context = this.getAttribute('data-context');
            var row = this.closest('tr');
            var delete_string = getString('delete', 'local_organization');
            var delete_advisor = getString('delete_advisor', 'local_organization');
            var cancel = getString('cancel', 'local_organization');
            var could_not_delete_advisor = getString('could_not_delete_advisor', 'local_organization');
            // Notification
            notification.confirm(delete_string, delete_advisor, delete_string, cancel, function () {
                // Delete the record
                var deleteAdvisor = ajax.call([{
                    methodname: 'local_organization_advisor_delete',
                    args: {
                        id: id,
                        role_id: role_id,
                        context: context
                    }
                }]);
                deleteAdvisor[0].done(function () {
                    row.remove();
                }).fail(function () {
                    notification.alert(could_not_delete_advisor);
                });
            });

        });
    });

}