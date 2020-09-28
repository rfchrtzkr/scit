dynamic json_received = Newtonsoft.(........);
string msg = json_received.msg;
if(msg == "success"){
    // new ransaction 
} else if(msg =="invalid_senior"){
    // pop up msg: "senior is invalid
} else if(msg =="invalid_drug"){
    // popup new form
    // dito ilalagay yung popup form
} else {
    // invalid communication with terminal
}

foreach(var drug in drugs){
    string generic_name = drug.generic_name;
    string brand = drug.brand;
    int dose = drug.dose;
    int unit = drug.unit;
}

public class Drug
{
    public string generic_name { get; set; }
    public string brand { get; set; }
    public int dose { get; set; }
    public string unit { get; set; }
}

Drug drug = JsonConvert.DeserializeObject<Drug>(json);

string json_string = @"[
    {
    'msg': 'success'
    }
]";

public class Drug_array {
    public string generic_name { get; set; } 
    public string brand { get; set; } 
    public string dose { get; set; } 
    public string unit { get; set; } 
}

public class Drug {
    public List<Drug_array> Drug_array { get; set; } 
}

Drug DeserializedDrug = Newtonsoft.Json.JsonConvert.DeserializeObject<Drug>(json_string); 

foreach(Drug d in DeserializedDrug){
    string generic_name = d.generic_name;
    string brand = d.brand;
    int dose = d.dose;
    int unit = d.unit;
    textBox1.Text += generic_name;
}

string is_OTC;
if(chkIsOTC.checked){
    is_OTC = "1";
} else {
    is_OTC = "0";
}

int selectedRow;
selectedRow = dgvNewDrugs.SelectedRows[0].Index;
dgvNewDrugs.Rows[selectedRow].SetValues(
    txtGenericname.Text, 
    txtBrand.Text,  
    txtDose.Text, 
    txtUnit.Text, 
    is_OTC,
    txtMaxWeekly.Text,
    txtMaxMonthly.Text);    
selectedRow++;
dgvNewDrugs.Rows[selectedRow].Selected = true;



if (true) //kung success yung POS to terminal
{

    string json_from_terminal = @"[
    {
        'msg': 'success'
    }
    ]";

    // string json_string = process_to_get_json_from_terminal_via_serial();

    public static dynamic json_received = Newtonsoft.Json.JsonConvert.DeserializeObject(json_from_terminal);
    string msg = json_received.msg;
    if (msg == "success")
    {
        // new ransaction 
        string a = "aa";
    }
    else if (msg == "invalid_senior")
    {
        // pop up msg: "senior is invalid
        string a = "aa";
    }
    else if (msg == "invalid_drug")
    {
        // popup new form
        // dito ilalagay yung popup form
        string a = "aa";
    }
    else
    {
        // invalid communication with terminal
        string a = "aa";
    }
    textBox1.Text = a;

    dynamic x = Newtonsoft.Json.JsonConvert.DeserializeObject(json_string);
    dynamic drugs = x.items;

    foreach (var drug in drugs)
    {
        string generic_name = drug.generic_name;
        string brand = drug.brand;
        int dose = drug.dose;
        string unit = drug.unit;
        //int is_otc = drug.is_otc;
        //int max_monthly = drug.max_monthly;
        //int max_weekly = drug.max_weekly;
        dt.Rows.Add(generic_name, brand, dose, unit);
        this.uutext.DataSource = dt;
    }
}


string json_string = @"{
'msg' : 'invalid_drug',
'items' :[
        {
            'clerk': 'AL Manalon',
            'generic_name': 'cetirizine',
            'brand': 'Watsons',
            'dose': '10',
            'unit': 'mg',
            'unit_price': '6.25',
            'quantity': '7',
            'vat_exempt_price': '39.06',
            'discount_price': '7.81',
            'payable_price': '31.25',
            'trans_date': '2020-09-17 21:11:11'
        },
        {
            'clerk': 'AL Manalon',
            'generic_name': 'Carbocisteine, Zinc',
            'brand': 'Solmux',
            'dose': '500',
            'unit': 'mg',
            'unit_price': '8.00',
            'quantity': '7',
            'vat_exempt_price': '50',
            'discount_price': '10',
            'payable_price': '40',
            'trans_date': '2020-09-17 21:11:11'
        },
        {
            'clerk': 'AL Manalon',
            'generic_name': 'paracetamol',
            'brand': 'BIOGESIC',
            'dose': '500',
            'unit': 'mg',
            'unit_price': '5.20',
            'quantity': '1',
            'vat_exempt_price': '65',
            'discount_price': '13',
            'payable_price': '52',
            'trans_date': '2020-09-17 21:11:11'
        },
        {
            'clerk': 'AL Manalon',
            'generic_name': 'sodium ascorbate,zinc',
            'brand': 'immunpro',
            'dose': '500',
            'unit': 'mg',
            'unit_price': '5.20',
            'quantity': '1',
            'vat_exempt_price': '65',
            'discount_price': '13',
            'payable_price': '52',
            'trans_date': '2020-09-17 21:11:11'
        }
    ]
}";


//frmNewDrug
public string json {get; set;}

btnSumit_Click()
{
    this.json = //jsonencode;
    this.DialogResult = DialogResult.OK;
    this.Close();
}

//main form, part where frmNewDrug is displayed

using (var form = frmNewDrug())
{
    var result = form.ShowDialog();
    if (result == DialogResult.OK)
    {
        string json = form.json;
    }
}