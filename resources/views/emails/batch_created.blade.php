<h3>New Batch Created</h3>

<p><strong>Batch Name:</strong> {{ $batch->name }}</p>
<p><strong>Customer:</strong> {{ $batch->customer->name ?? '-' }}</p>
<p><strong>Start Date:</strong> {{ $batch->start_date }}</p>
<p><strong>End Date:</strong> {{ $batch->end_date ?? 'N/A' }}</p>
<p><strong>Status:</strong> {{ ucfirst($batch->status) }}</p>

<p>Created By: {{ $createdBy }}</p>

<p>Thank you,<br>UNIBS LMS Team</p>
