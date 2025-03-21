<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Container from "../../../Components/Container.vue";
import Title from "../../../Components/Title.vue";

// Set the layout for this component
defineOptions({ layout: AdminLayout });

const props = defineProps({
  leadBookingData: {
    type: Object,
    required: true,
    default: () => ({
      pbBooking: [],
      pbCode: [],
      pbServicePrice: [],
      pbUnitContact: [],
      countUnread: 0,
      countNewInspection: 0,
      CountReInspection: 0,
      CountPayInvoice: 0,
      CountPayElectronics: 0,
      CountAgentBooking: 0,
      CountWebBooking: 0,
      CountAssignJobs: 0,
      CountPendingAssign: 0,
      CountBookingInspectors: 0,

      CountInspectors: 0,
      CountDiscountCode: 0,
      CountInspectors: 0,
      CountServices: 0,
      // hhaPaymentPendingCount: 0,
      // medPaymentPendingCount: 0,
      // blsPaymentPendingCount: 0,

      totalPaid: 0,
      totalUnpaid: 0,

      finalReview: 0,
      reportSent: 0,
      expire90: 0,
      CountInsComp: 0,

    }),
  },
});

// Method to generate the download link
const getDownloadLink = (filename) => {
  return `/docs/${filename}`;
};
</script>

<template>
    <Head title="- Select a payment method" />
    <Container class="w-full max-w-4xl mx-auto">
      <div class="mb-8 text-center">
        <Title>Admin Dashboard</Title>
      </div>
      <hr>

      <div>
        <h1 class="text-2xl font-bold mb-4">Welcome Admin</h1>
        <!-- Your admin dashboard content -->
      </div>

      <div class="mt-8">
        <h2 class="text-xl font-bold mb-4">Lead Inspection - Summary of Booking Activities</h2>
        <!-- Your recent activities content -->
        
        <!-- Responsive Grid for Activities -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Example grid items, replace with your actual content -->
          <Link :href="route('admin.bookings')" class="text-gray-900">
            <div class="bg-orange-200 font-bold text-center p-4 shadow rounded">All to Date
              <button class="mt-0 mb-0 bg-gray-200 py-8 px-4 rounded w-full">
                <p class="text-center text-5xl">{{ leadBookingData.pbBooking ? leadBookingData.pbBooking.length : '--' }}</p>
              </button>
            </div>
          </Link>

          <Link :href="route('admin.dashboard')" class="text-gray-900">
            <div class="bg-gray-100 font-bold text-center p-4 shadow rounded">New Bookings
              <button class="mt-0 mb-0 bg-gray-200 py-8 px-4 rounded w-full">
                <p class="text-center text-5xl">{{ leadBookingData.countUnread || '--' }}</p>
              </button>
            </div>
          </Link>
          
          <Link :href="route('admin.dashboard')" class="text-gray-900">
            <div class="bg-gray-100 font-bold text-center p-4 shadow rounded">Not Assign
              <button class="mt-0 mb-0 bg-gray-200 py-8 px-4 rounded w-full">
                <p class="text-center text-5xl">{{ leadBookingData.CountPendingAssign || '--' }}</p>
              </button>
            </div>
          </Link>

          <Link :href="route('admin.dashboard')" class="text-gray-900">
            <div class="bg-gray-100 font-bold text-center p-4 shadow rounded">Assigned to Agents
              <button class="mt-0 mb-0 bg-gray-200 py-8 px-4 rounded w-full">
                <p class="text-center text-5xl">{{ leadBookingData.CountAssignJobs || '--' }}</p>
              </button>
            </div>
          </Link>

          <Link :href="route('admin.dashboard')" class="text-gray-900">
            <div class="bg-gray-100 font-bold text-center p-4 shadow rounded">Reg. Bookings
              <button class="mt-0 mb-0 bg-gray-200 py-8 px-4 rounded w-full">
                <p class="text-center text-5xl">{{ leadBookingData.CountWebBooking || '--' }}</p>
              </button>
            </div>
          </Link>

          <Link :href="route('admin.dashboard')" class="text-gray-900">
            <div class="bg-gray-100 font-bold text-center p-4 shadow rounded">With Discount Code
              <button class="mt-0 mb-0 bg-gray-200 py-8 px-4 rounded w-full">
                <p class="text-center text-5xl">{{ leadBookingData.CountAgentBooking || '--' }}</p>
              </button>
            </div>
          </Link>

          <Link :href="route('admin.dashboard')" class="text-gray-900">
            <div class="bg-gray-100 font-bold text-center p-4 shadow rounded"> Incoming Payments
              <button class="mt-0 mb-0 bg-gray-200 py-8 px-4 rounded w-full">
                <p class="text-center text-5xl">{{ leadBookingData.CountPayInvoice || '--' }}</p>
              </button>
            </div>
          </Link>

          <Link :href="route('admin.dashboard')" class="text-gray-900">
            <div class="bg-gray-100 font-bold text-center p-4 shadow rounded">Complete Payments
              <button class="mt-0 mb-0 bg-gray-200 py-8 px-4 rounded w-full">
                <p class="text-center text-5xl">{{ leadBookingData.CountPayElectronics || '--' }}</p>
              </button>
            </div>
          </Link>          

        </div>
      </div> 

      <div class="mt-8">
        <h2 class="text-xl font-bold mb-4">Booking Calendar</h2>
        <!-- Calendar -->
        
        <!-- Responsive Grid for Activities -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
          <!-- Example grid items, replace with your actual content -->
          <Link :href="route('admin.scheduled_bookings')" class="text-gray-900">
            <div class="bg-indigo-200 font-bold text-center p-4 shadow rounded">All
              <button class="mt-0 mb-0 bg-gray-200 py-8 px-4 rounded w-full">
                <p class="text-center text-5xl">{{ leadBookingData.pbBooking ? leadBookingData.pbBooking.length : '--' }}</p>
              </button>
            </div>
          </Link>

          <Link  class="text-gray-900">
            <div class="bg-indigo-200 font-bold text-center p-4 shadow rounded">Agents & Inspectors
              <button class="mt-0 mb-0 bg-gray-200 py-8 px-4 rounded w-full">
                <p class="text-center text-5xl">{{ leadBookingData.CountBookingInspectors || '--' }}</p>
              </button>
            </div>
          </Link>
          
        </div>
      </div>   

      <div class="mt-8">
        <h2 class="text-xl font-bold mb-4">Reports, Activities & Certificates</h2>
        <!-- Your recent activities content -->
        
        <!-- Responsive Grid for Activities -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
          <!-- Example grid items, replace with your actual content -->
          <Link :href="route('admin.inspection.completed')" class="text-gray-900">
            <div class="bg-gray-100 font-bold p-4 shadow rounded">
              Inspection Completed - Doc. Pending<p class="text-center text-5xl">{{ leadBookingData.CountInsComp || '--' }}</p>
            </div>
          </Link>
          
          <Link :href="route('admin.inspection.review_needed')" class="text-gray-900">
            <div class="bg-gray-100 font-bold p-4 shadow rounded">
              Admin Review Needed<p class="text-center text-5xl">{{ leadBookingData.finalReview || '--' }}</p>
            </div>
          </Link>
          <Link :href="route('admin.inspection.report_sent')" class="text-gray-900">
            <div class="bg-gray-100 font-bold p-4 shadow rounded">
              Report Sent <p class="text-center text-5xl">{{ leadBookingData.reportSent || '--' }}</p>
            </div>
          </Link>
          <Link :href="route('admin.inspection.expire90')" class="text-gray-900">
            <div class="bg-gray-100 font-bold p-4 shadow rounded">
              Expiring in 90 Days<p class="text-center text-5xl">{{ leadBookingData.expire90 || '--' }}</p>
            </div>
          </Link>

        </div>
      </div>   

      <div class="mt-8">
        <h2 class="text-xl font-bold mb-4">Portal</h2>
        <!-- Your recent activities content -->
        
        <!-- Responsive Grid for Activities -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
          <!-- Example grid items, replace with your actual content -->
          <Link :href="route('admin.inspectors.index')" class="text-gray-900">
            <div class="bg-gray-100 font-bold p-4 shadow rounded">
              Agents & Inspectors<p class="text-center text-5xl">{{ leadBookingData.CountInspectors || '--' }}</p>
            </div>
          </Link>
          
          <Link :href="route('admin.discount.code')" class="text-gray-900">
            <div class="bg-gray-100 font-bold p-4 shadow rounded">
              Discount Codes<p class="text-center text-5xl">{{ leadBookingData.CountDiscountCode || '--' }}</p>
            </div>
          </Link>
          
          <Link :href="route('admin.service.index')" class="text-gray-900">
            <div class="bg-gray-100 font-bold p-4 shadow rounded">
              Set Services & Price<p class="text-center text-5xl">{{ leadBookingData.CountServices || '--' }}</p>
            </div>
          </Link>
          
          <Link :href="route('admin.service.sign_code')" class="text-gray-900">
            <div class="bg-gray-100 font-bold p-4 shadow rounded">
              New Agent Sign-up Code<p class="text-center text-5xl">{{ leadBookingData.CountTeamCode || '--' }}</p>
            </div>
          </Link>
        </div>
      </div>   

      <div class="mt-8">
        <h2 class="text-xl font-bold mb-4">Finance</h2>
        <!-- Your recent activities content -->
        
        <!-- Responsive Grid for Activities -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
          <!-- Example grid items, replace with your actual content -->
          <div class="bg-gray-100 font-bold p-4 shadow rounded">Paid
            <p class="text-center text-5xl">${{ leadBookingData.totalPaid || '--' }}</p>
          </div>
          <div class="bg-gray-100 font-bold p-4 shadow rounded">Incoming
            <p class="text-center text-5xl">${{ leadBookingData.totalUnpaid || '--' }}</p>
          </div>
        </div>
      </div>   
      <div class="mt-8">
        <h2 class="text-xl font-bold mb-4">Documents & Certificates</h2>
        <!-- Your recent activities content -->
        
        <!-- Responsive Grid for Activities -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
          <!-- Example grid items, replace with your actual content -->
          <a 
                            :href="getDownloadLink('Floor__Plan.pdf')" 
                            target="_blank"
                            rel="noopener noreferrer"
                            >
              <div class="bg-gray-100 font-bold p-4 shadow rounded">Floor Plan
                <p class="text-center">Link to Download</p>
              </div>
          </a>
          <a 
                            :href="getDownloadLink('Chain__of__Custody.pdf')" 
                            target="_blank"
                            rel="noopener noreferrer"
                            >
              <div class="bg-gray-100 font-bold p-4 shadow rounded">Chain of Custody
                <p class="text-center">Link to Download</p>
              </div>
          </a>
          <a 
                            :href="getDownloadLink('Contractor_Information_Form.pdf')" 
                            target="_blank"
                            rel="noopener noreferrer"
                            >
              <div class="bg-gray-100 font-bold p-4 shadow rounded">Contractor Information Form
                <p class="text-center">Link to Download</p>
              </div>
          </a>
          <a 
                            :href="getDownloadLink('Inspector_Contract_Policy.pdf')" 
                            target="_blank"
                            rel="noopener noreferrer"
                            >
              <div class="bg-gray-100 font-bold p-4 shadow rounded">Inspector Contract Policy
                <p class="text-center">Link to Download</p>
              </div>
          </a>
          <a 
            href="https://www.irs.gov/pub/irs-pdf/fw9.pdf" 
            target="_blank" 
            rel="noopener noreferrer"
          >
            <div class="bg-gray-100 font-bold p-4 shadow rounded">
              Tax Doc:
              <p class="text-center">Download W-9</p>
            </div>
          </a>
          <a 
            href="https://www.nj.gov/dca/codes/forms/pdf_lead/lead_safe_cert_fill.pdf" 
            target="_blank" 
            rel="noopener noreferrer"
          >
            <div class="bg-gray-100 font-bold p-4 shadow rounded">
              Lead - SAFE CERTIFICATE:
              <p class="text-center">Download Lead-Safe Certificate</p>
            </div>
          </a>

        </div>
      </div>   

    </Container>
</template>
  