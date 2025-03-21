class AttendanceManager {
  constructor() {
    this.attendanceCardsContainer = document.getElementById(
      "attendance-cards-container"
    );
    this.init();
  }

  init() {
    setInterval(() => this.fetchAttendanceData(), 3000);
  }

  renderStaffAttendance(staffData) {
    let staffDataContent = ``;

    staffData.forEach((staffItem) => {
      staffDataContent += `
                <div class="col-sm-4">
                    <div class="card border" style="overflow: hidden; border-radius: 5px;">
                        <img src="${staffItem.image}" style="max-height: 180px; object-fit: cover; object-position: center;" loading="lazy">
                        <div class="card-body">
                            <h5 class="card-title">${staffItem.fullName}</h5>
                            <div class="group">
                                <p class="card-text text-muted">Arrived At ${staffItem.arrival_time}</p>
                            </div>
                            <div class="group">
                                <p class="card-text text-muted">Departed At ${staffItem.departure_time}</p>
                            </div>
                            <div class="group">
                                <div class="d-flex align-items-center gap-1 text-success my-1">
                                    <i class="bx bx-check-circle text-success" style="font-size: 18px;"></i>
                                    <span>
                                        Attendance Marked
                                    </span>
                                </div>
                            </div>
                            <div class="text-dark mt-1" style="font-weight: bold;">${staffItem.time_late}</div>
                        </div>
                    </div>
                </div>
            `;
    });

    this.attendanceCardsContainer.innerHTML = staffDataContent;

    if (staffData.length == 0) {
      this.attendanceCardsContainer.innerHTML = `
      <div class="d-flex align-items-center justify-content-center" style="height: 250px;">
       <span style="font-weight: bold;">No Attendances Registered Today Yet...</span>
      </div>
      `;
    }
  }

  fetchAttendanceData() {
    axios
      .get("http://localhost/icuc_ars/api/attendance/currently_attending")
      .then((response) => {
        console.log(response.data);
        this.renderStaffAttendance(response.data);
      })
      .catch((err) => {
        this.attendanceCardsContainer.innerHTML = `
          <div class="d-flex align-items-center justify-content-center" style="height: 250px;">
            <span style="font-weight: bold;">An Error Occurred, Couldn't retrieve the data</span>
          </div>
            
        `;
        console.log(err);
      });
  }
}
