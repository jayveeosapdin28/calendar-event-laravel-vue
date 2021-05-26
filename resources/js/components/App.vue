<template>
    <div class="p-2">
        <div class="card p-2">
            <div class="card-title">
                <h2 class="fw-bold">Event Calendar</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <!--form-->
                        <form @submit.prevent="saveData">
                            <div class="form">
                                <label for="eventTitle" class="fw-bold fs-5">Event</label>
                                <input required v-model="name" id="eventTitle" type="text" class="form-control">
                                <div class="row my-2">
                                    <div class="col-md-6">
                                        <label for="dateFrom" class="fw-bold fs-5">From</label>
                                        <input required v-model="from" id="dateFrom" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" class="form-control"></div>
                                    <div class="col-md-6">
                                        <label for="dateTo" class="fw-bold fs-5">To</label>
                                        <input required v-model="to" id="dateTo" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" class="form-control">
                                    </div>
                                </div>
                                <div class="my-3">
                                    <!--Day in a week checkBox-->
                                    <div v-for="day in weekdaysCheckBox" :key="day.id" class="form-check">
                                        <input v-model="eventDays" :id="day.id" class="form-check-input" type="checkbox" :value="day.label">
                                        <label class="form-check-label" :for="day.id" >
                                            {{ day.label }}
                                        </label>
                                    </div>
                                    <!--End of Day in a week checkBox-->
                                </div>
                                <div>
                                    <button type="submit" class="form-control btn btn-info text-white text-uppercase">Save</button>
                                </div>
                            </div>
                        </form>
                        <!--end form-->
                    </div>
                    <div class="col-md-9">
                        <div>
                            <h3 class="fw-bold">{{getMonthYear()}}</h3>
                        </div>
                        <div>
                            <table class="table table-hover table-striped ">
                                <tbody>
                                <tr v-for="day in daysInMonth">
                                    <td style="width: 1rem"><span class="fs-4 fw-bold">{{day}}</span> </td>
                                    <td style="width: 1rem" class="">
                                        {{getDaysOfWeek(day)}}
                                    </td>
                                    <td>
                                        <div v-if="savedEvent">
                                            <div v-if="day >= splitInputDate(savedEvent.from)[2] && day <= splitInputDate(savedEvent.to)[2]">
                                                <div v-if="splitSavedDay(savedEvent.day).indexOf(getDaysOfWeek(day)) !== -1" class="alert alert-success" role="alert">
                                                    <p class="w-100 " >
                                                        {{savedEvent.name}}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
const current = new Date();
export default {
    name: "App",
    data: () =>({
        name: null,
        from: null,
        to: null,
        title: null,
        eventDays: [],
        savedEvent: null,
        weekdaysCheckBox: [
            {id: 'sunday',label: 'Sun'},
            {id: 'monday',label: 'Mon'},
            {id: 'tuesday',label: 'Tue'},
            {id: 'wednesday',label: 'Wed'},
            {id: 'thursday',label: 'Thu'},
            {id: 'friday',label: 'Fri'},
            {id: 'saturday',label: 'Sat'},
        ],
        daysInMonth: 0,
        calendar: {
            weekdays: ["Sun","Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
            month: [ "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December" ],
        },

    }),
    methods:{
        getDaysInMonth(month,year){
            return new Date(year, month, 0).getDate();
        },
        getMonthYear(){
            return this.calendar.month[current.getMonth()]+" "+current.getFullYear();
        },
        getDaysOfWeek(day){
            var date = new Date(current.getFullYear(),current.getMonth(),day );
            return this.calendar.weekdays[date.getDay()];
        },
        splitInputDate(inputDate){
            return inputDate.split("-");
        },
        splitSavedDay(day){
            return day.split(",");
        },
        validateDateRange(){
            var splitedDateFrom = this.splitInputDate(this.from)
            var splitedDateTo = this.splitInputDate(this.to)
            var from = new Date(splitedDateFrom[2],splitedDateFrom[1],splitedDateFrom[0])
            var to = new Date(splitedDateTo[2],splitedDateTo[1],splitedDateTo[0])

            return from <= to ? true : false;
        },
        validateForm(){
            if(this.name.length > 100){
                this.showToaster('error','Oops! Event name reach the maximum length.')
                return false;
            }
            if(this.eventDays.length < 1 ){
                this.showToaster('error','Oops! Please select event days.')
                return false;
            }
            return  true ;
        },
        showToaster(type,message){
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                timer: 3000,
                showConfirmButton: false,
            })

            Toast.fire({
                icon: type,
                title: message
            })
        },
        saveData(){
            if(this.validateForm()){
                if(this.validateDateRange()){
                    this.event = {
                        name: this.name,
                        from: this.from,
                        to: this.to,
                        day: this.eventDays
                    }
                    axios.post('/api/event/add-event',this.event).then((response) => {
                        this.savedEvent = response.data;
                    })
                        .catch(error => {
                            if (error.response.status == 422){
                                for (error in error.response.data.errors){
                                    console.log(error.name)
                                }
                            }
                        });
                }
                else{
                    this.showToaster('error','Oops! Error found in adding event!')
                }
                this.showToaster('success','Awesome! Event is added successfully!')
            }
        }

    },
    mounted() {
        this.daysInMonth = this.getDaysInMonth(current.getMonth()+1,current.getFullYear());
    }
}
</script>

<style scoped>

</style>
