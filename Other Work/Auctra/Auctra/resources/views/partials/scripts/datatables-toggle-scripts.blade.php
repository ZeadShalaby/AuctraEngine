<script>
    $(document).on(
        'change',
        '.toggle-status, .toggle-notification, .toggle-email, .toggle-auction, .toggle-ads',
        function() {

            let id = $(this).data('id');

            let key =
                $(this).hasClass('toggle-status') ? 'status' :
                $(this).hasClass('toggle-notification') ? 'notifications_enabled' :
                $(this).hasClass('toggle-email') ? 'email_enabled' :
                $(this).hasClass('toggle-ads') ? 'ads_enabled' :
                $(this).hasClass('toggle-auction') ? 'auction_enabled' :
                null;

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

                        badge.removeClass(
                            'bg-success bg-danger bg-dark bg-secondary'
                        );

                        badge.addClass('bg-' + response.color);
                        badge.text(response.status);

                    } else {

                        let badgeClass =
                            key === 'notifications_enabled' ? '.notification-badge' :
                            key === 'email_enabled' ? '.email-badge' :
                            key === 'ads_enabled' ? '.ads-badge' :
                            key === 'auction_enabled' ? '.auction-badge' : null;

                        let badge = $(badgeClass + '[data-id="' + id + '"]');

                        badge.removeClass('bg-success bg-danger');
                        badge.addClass('bg-' + response.color);

                        badge.text(
                            response.value ? 'Enabled' : 'Disabled'
                        );
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        }
    );
</script>
