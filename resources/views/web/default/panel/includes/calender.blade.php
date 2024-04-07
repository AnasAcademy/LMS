<style>
    .container_cal {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    #current-month-year {
        text-align: center;
        margin-bottom: 10px;
        color: #333;
        font-size: 24px;
        margin-top: 0;
    }

    .row_cal {
        margin-bottom: 20px;
    }

    .col_cal {
        padding: 0 15px;
    }

    .d-flex_cal {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .table_cal {
        width: 100%;
        border-collapse: collapse;
    }

    .table_cal th,
    .table_cal td {
        padding: 10px;
        border: 1px solid #ccc;
        text-align: center;
    }

    .table_cal th {
        background-color: #f2f2f2;
    }

    button {
        padding: 5px 10px;
        border: 1px solid #ccc;
        background-color: #f2f2f2;
        cursor: pointer;
        color: #333;
        transition: background-color 0.3s, color 0.3s;
        border-radius: 3px;
    }

    button:hover {
        background-color: #e6e6e6;
    }
</style>

<div class="container_cal">
    <h1 id="current-month-year">Calendar</h1>
    <div class="row_cal">
        <div class="col_cal">
            <div class="d-flex_cal justify-content-between_cal">
                <button class="btn btn-primary" id="prev-month">Previous Month</button>
                <button class="btn btn-primary" id="next-month">Next Month</button>
            </div>
            <table class="table table-bordered">
                <thead class="_cal">
                    <tr class="_cal">
                        <th class="_cal">Sun</th>
                        <th class="_cal">Mon</th>
                        <th class="_cal">Tue</th>
                        <th class="_cal">Wed</th>
                        <th class="_cal">Thu</th>
                        <th class="_cal">Fri</th>
                        <th class="_cal">Sat</th>
                    </tr>
                </thead>
                <tbody id="calendar-body"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Popup -->
<div class="modal fade" id="bundlePopup" tabindex="-1" role="dialog" aria-labelledby="bundlePopupLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bundlePopupLabel">المقررات في هذا اليوم </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="bundleStartDate"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    function dateTimeFormat(timestamp) {
        const date = new Date(timestamp * 1000);
        const year = date.getFullYear();
        const month = ('0' + (date.getMonth() + 1)).slice(-2);
        const day = ('0' + date.getDate()).slice(-2);

        return `${year}-${month}-${day}`;
    }
    $(document).ready(function() {
        const calendarBody = $('#calendar-body');
        const bundlePopup = $('#bundlePopup');
        const currentMonthYear = $('#current-month-year');

        const bundles = @json($webinars); // Assuming $webinars contains your event data

        let currentDate = new Date();
        let currentYear = currentDate.getFullYear();
        let currentMonth = currentDate.getMonth();

        updateCalendar();

        $('#prev-month').click(function() {
            if (currentMonth === 0) {
                currentMonth = 11;
                currentYear--;
            } else {
                currentMonth--;
            }
            updateCalendar();
        });

        $('#next-month').click(function() {
            if (currentMonth === 11) {
                currentMonth = 0;
                currentYear++;
            } else {
                currentMonth++;
            }
            updateCalendar();
        });

        function updateCalendar() {
    currentMonthYear.text(`${currentYear}-${currentMonth + 1}`);
    calendarBody.empty();

    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    const firstDayOfWeek = new Date(currentYear, currentMonth, 1).getDay();

    let currentDay = 1;

    for (let i = 0; currentDay <= daysInMonth; i++) {
        const row = $('<tr>');
        for (let j = 0; j < 7; j++) {
            if ((i === 0 && j < firstDayOfWeek) || currentDay > daysInMonth) {
                row.append('<td></td>');
            } else {
                const date = new Date(currentYear, currentMonth, currentDay);
                const formattedDate = formatDate(date);
                const allBundles = bundles.filter(bundle => formatDate(new Date(bundle.start_date * 1000)) === formattedDate);
                const dayClass = (allBundles.length > 0) ? 'day-with-bundle' : '';

                let details = "";
                for (bundle of allBundles) {
                    details += bundle.title + "<br>";
                }

                row.append(`<td class="${dayClass}" data-date="${formattedDate}">
                    ${currentDay}
                    <p class='course-title'>${details}</p>
                </td>`);
                currentDay++;
            }
        }
        calendarBody.append(row);
    }

    $('.day-with-bundle').off('click').on('click', function() {
        const date = $(this).data('date');
        const bundlesData = bundles.filter(bundle => formatDate(new Date(bundle.start_date * 1000)) === date);

        let text = "";
        for (bundle of bundlesData) {
            text += bundle.title + "<br>";
        }
        $('#bundleStartDate').html(`${text}`);
        bundlePopup.modal('show');
    });
}




        function formatDate(date) {
            const date2 = new Date(date);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, "0");
            const day = String(date.getDate()).padStart(2, "0");
            const formattedDate = `${year}-${month}-${day}`;

            return formattedDate;
        }
    });
</script>
