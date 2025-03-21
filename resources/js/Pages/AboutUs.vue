<script setup>
// import { ref, computed, onMounted } from 'vue';
import Container from "../Components/Container.vue";
import Title from "../Components/Title.vue";

// Access the APP_NAME environment variable directly
const companyName = import.meta.env.VITE_APP_NAME || 'Default Company Name'; // Default if not set

</script>

<template>
    <Head title="- About Us" />
    <Container class="w-3/5">
        <div class="mb-8 text-center">
            <Title>About Us</Title>
        </div>

        <!-- About us content -->
        <div class="grid grid-cols-1 gap-8">
            <p class="text-slate-900 text-medium dark:text-slate-200">
                {{ companyName }} is committed to promoting environmental safety and public health through expert lead evaluation and assessment services. Our team comprises industry professionals with extensive experience in lead hazard identification, risk assessment, and remediation strategies. With a dedication to regulatory compliance and public safety, we provide reliable solutions to protect communities from lead exposure.
            </p>
            <p class="text-slate-900 text-medium dark:text-slate-200">
                Our specialists have completed numerous lead assessment projects, ensuring adherence to federal, state, and local regulations. We conduct comprehensive lead inspections, risk assessments, and clearance examinations for residential, commercial, and public properties. Our strong background in government-funded lead hazard reduction programs enables us to deliver accurate evaluations and remediation recommendations that safeguard communities.
            </p>
        </div>      
    </Container>

    <Container class="w-3/5 mt-4">
        <div class="mt-2 text-center">
            <Title>Our Expertise Includes</Title>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <ul class="list-none col-span-1">
                <li class="flex items-start space-x-2">
                    <span class="inline-block w-2 h-2 bg-black rounded-full mt-1"></span>
                    <div>
                        <h3 class="font-bold">Lead-based Paint Inspections & Risk Assessments</h3>
                        <p>We use advanced XRF technology to conduct detailed inspections and risk assessments to identify lead-based paint hazards.</p>
                    </div>
                </li>
                <li class="flex items-start space-x-2">
                    <span class="inline-block w-2 h-2 bg-black rounded-full mt-1"></span>
                    <div>
                        <h3 class="font-bold">Soil and Water Testing</h3>
                        <p>We provide soil and water testing services to detect lead contamination and assess the safety of the environment.</p>
                    </div>
                </li>
                <li class="flex items-start space-x-2">
                    <span class="inline-block w-2 h-2 bg-black rounded-full mt-1"></span>
                    <div>
                        <h3 class="font-bold">Development of Lead Hazard Control Plans</h3>
                        <p>We help develop effective lead hazard control plans tailored to mitigate lead exposure risks.</p>
                    </div>
                </li>
                <li class="flex items-start space-x-2">
                    <span class="inline-block w-2 h-2 bg-black rounded-full mt-1"></span>
                    <div>
                        <h3 class="font-bold">Abatement and Remediation Oversight</h3>
                        <p>We oversee abatement and remediation projects to ensure that lead hazards are effectively removed and controlled.</p>
                    </div>
                </li>
                <li class="flex items-start space-x-2">
                    <span class="inline-block w-2 h-2 bg-black rounded-full mt-1"></span>
                    <div>
                        <h3 class="font-bold">Community Outreach & Education</h3>
                        <p>We offer community outreach programs and education to raise awareness about lead safety best practices and prevention strategies.</p>
                    </div>
                </li>
            </ul>
            
            <div class="col-span-1">
                <p class="text-slate-900 text-medium dark:text-slate-200 mb-4 mt-4">
                    As a New Jersey DCA-certified and EPA-certified lead evaluation firm, {{ companyName }} is licensed, insured, and staffed with professionally trained and licensed team members. With a proven track record of delivering accurate assessments, ensuring regulatory compliance, and implementing effective risk mitigation strategies, we remain committed to providing trustworthy environmental solutions.
                </p>
                <p class="text-slate-900 text-medium dark:text-slate-200">
                    {{ companyName }} is poised to be a trusted partner for municipalities, housing agencies, and private clients seeking expert lead assessment services. Our mission is to create healthier environments by identifying and mitigating lead hazards, ultimately protecting public health and enhancing community well-being.
                </p>
            </div>
        </div>
    </Container>
</template>