<script>
    $(document).on('change', '.toggle-status, .toggle-notification, .toggle-email', function() {
                let id = $(this).data('id');
                let key = $(this).hasClass('toggle-status') ? 'status' :
                    $(this).hasClass('toggle-notification') ? 'notifications_enabled' : 'email_enabled';
                $.ajax({
                    url: '{{ route('users.toggle', ['user' => ':id']) }}'.replace(':id', id),
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: id,
                        key: key
                    },
                    success: function(response) {
                        if (key === 'status') {
                            let badge = $('.status-badge[data-id="' + id + '"]');
                            badge.removeClass('bg-success bg-danger bg-dark bg-secondary');
                            badge.addClass('bg-' + response.color);
                            badge.text(response.status);
                        } else {
                            console.log('Toggled ' + key + ' to', response.value);
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            });
</script>