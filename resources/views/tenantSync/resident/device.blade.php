@extends('TenantSync::resident/layout')

@section('head')
	<meta id="user_id" value="{{ $user->id }}">
@endsection


@section('content')

<div v-cloak>
	
	<devices-table inline-template>
		@include('TenantSync::includes.tables.devices-table')
	</devices-table>

</div>

@endsection

@section('scripts')
<script>
Vue.config.debug = true;

var vue = new Vue({
	el: '#app',

	data: {
		showStat: {
			paid_rent: false,
			deliquent_rent: false,
			vacant_rent: false,
		},

		devices: [],

		transactions: [],

		rentBills: [],
	},

	computed: {
		stats: function() {
			return {
				alarms: this.alarms(),
				paid_rent: this.paidRent(),
				deliquent_rent: this.deliquentRent(),
				vacant_rent: this.vacantRent(),
			};
		}
	},

	ready: function() {
		this.fetchDevices();
		this.fetchTransactions();
		this.fetchRentBills();
	},

	events: {
		'modal-hidden': function() {
			this.hideStats();
		}
	},

	methods: {
		fetchDevices: function() {
			var data = {
				set: ['rent_owed'],
			};

			this.$http.get('/api/devices', data)
				.success(function(devices) {
					this.devices = devices;
				});
		},

		fetchTransactions: function() {
			var data = {
				from: '-1 month',
				set: ['address']
			};

			this.$http.get('/api/transactions', data)
				.success(function(transactions) {
					this.transactions = transactions;
				});
		},

		fetchRentBills: function() {
			var data = {
				with: ['device'],
				from: '-1 month',
			};

			this.$http.get('/api/rent-bills', data)
				.success(function(rentBills) {
					this.rentBills = rentBills;
				});
		},

		toggleStat: function(stat) {
			this.showStat[stat] = !this.showStat[stat];
			this.$broadcast('show-modal');
		},

		hideStats: function() {
			for(var i = 0; i < _.size(this.showStat); i++) {
				var key = Object.keys(this.showStat)[i];

				this.showStat[key] = false;
			}
		},

		alarms: function() {
			var devices = _.filter(this.devices, function(device) {
				return device.alarm_id;
			});

			return devices.length;
		},

		paidRentTransactions: function() {
			return _.filter(this.transactions, function(transaction) {
				var from = Number(moment().subtract(1, 'month').format('X'));

				var transactionDate = Number(moment(transaction.date).format('X'));

				if(from < transactionDate && transaction.payable_type == 'TenantSync\\Models\\Device') {
					return true;
				}

				return false;
			});
		},

		paidRent: function() {
			var transactions = this.paidRentTransactions();

			return _.reduce(transactions, function(initial, transaction) {
				return initial + Number(transaction.amount);
			}, 0);
		},

		deliquentDevices: function() {
			return _.filter(this.devices, function(device) {
				return device.rent_owed > 0;
			}); 
		},

		deliquentRent: function() {
			return _.reduce(this.deliquentDevices(), function(carry, device) {
				return carry + device.rent_owed;
			}, 0);
		},

		vacantRentBills: function() {
			return _.filter(this.rentBills, function(bill) {
				return bill.vacant;
			});
		},

		vacantRent: function() {
			var bills = this.vacantRentBills();

			return _.reduce(bills, function(initial, bill) {
				return initial + Number(bill.bill_amount);
			}, 0);
		},
	},
});
</script>
@endsection