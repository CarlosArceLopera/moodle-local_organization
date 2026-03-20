import notification from 'core/notification';
import ajax from 'core/ajax';
import {get_string as getString} from 'core/str';

export const init = () => {
    deleteDepartment();
};

/**
 * Delete Department
 */
function deleteDepartment() {
    // Pop-up notification when .btn-local-organization-delete-campus is clicked
    document.querySelectorAll('.btn-local-organization-delete-department').forEach(button => {
        button.addEventListener('click', function () {
            // Get the data id attribute value
            var id = this.getAttribute('data-id');
            var row = this.closest('tr');
            var delete_string = getString('delete', 'local_organization');
            var delete_unit = getString('delete_department', 'local_organization');
            var cancel = getString('cancel', 'local_organization');
            var could_not_delete_unit = getString('could_not_delete_department', 'local_organization');
            // Notification
            notification.confirm(delete_string, delete_unit, delete_string, cancel, function () {
                // Delete the record
                var deleteCampus = ajax.call([{
                    methodname: 'local_organization_department_delete',
                    args: {
                        id: id
                    }
                }]);
                deleteCampus[0].done(function () {
                    row.remove();
                }).fail(function () {
                   notification.alert(could_not_delete_unit);
                });
            });

        });
    });

}