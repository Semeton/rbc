#!/bin/bash

# Script to update all PDF export actions to use Spatie PDF

for file in app/Actions/Export*Pdf.php; do
    echo "Updating $file..."
    
    # Replace the execute method with Spatie PDF implementation
    sed -i '' '/public function execute/,/^    }/c\
    public function execute(array $filters = []): Response\
    {\
        $report = new '$(basename "$file" .php | sed 's/Export//' | sed 's/Pdf//')'Report;\
        $data = $report->generate($filters);\
        $summary = $report->getSummary($filters);\
\
        $html = $this->generateHtml($data, $summary, $filters);\
\
        $filename = '\''$(echo "$file" | sed "s/.*Export//" | sed "s/Pdf.php//" | tr "[:upper:]" "[:lower:]")'-report-'\''.now()->format('\''Y-m-d_H-i-s'\'').'\''.pdf'\'';\
\
        $pdf = Pdf::html($html)\
            ->format('\''A4'\'')\
            ->landscape()\
            ->margins(20, 20, 20, 20)\
            ->download($filename);\
\
        return $pdf->toResponse(request());\
    }' "$file"
    
    # Replace N currency symbols with ₦
    sed -i '' 's/N'\''.number_format/₦'\''.number_format/g' "$file"
    
    # Update font family
    sed -i '' 's/font-family: DejaVu Sans, Arial, sans-serif;/font-family: Arial, sans-serif;/g' "$file"
done

echo "All PDF export actions updated!"
