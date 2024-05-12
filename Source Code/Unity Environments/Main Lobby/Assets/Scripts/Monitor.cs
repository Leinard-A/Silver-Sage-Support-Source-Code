using System;
using System.Collections;
using System.Collections.Generic;
using TMPro;
using UnityEngine;
using UnityEngine.Networking;

public class Monitor : MonoBehaviour
{
    public string[] patients;
    public List<string> names = new List<string>();

    public static string Name = "";
    public static string Email = "";
    public static int NHS_Number = 0;
    public static int ID = 0;
    public static int Employee = 0;

    [SerializeField] GameObject nameLabel;
    [SerializeField] GameObject emailLabel;
    [SerializeField] GameObject nhsLabel;
    [SerializeField] GameObject content;
    [SerializeField] GameObject employeeTag;

    // Start is called before the first frame update
    void Start()
    {        
        if (Employee == 1)
        {
            nameLabel.GetComponent<TextMeshProUGUI>().text = Name;
            emailLabel.GetComponent<TextMeshProUGUI>().text = Email;
            nhsLabel.GetComponent<TextMeshProUGUI>().text = NHS_Number.ToString();

            employeeTag.SetActive(true);
            StartCoroutine(GetData());
        }        

        //StartCoroutine(GetData());        
    }

    IEnumerator GetData()
    {
        using (UnityWebRequest www = UnityWebRequest.Get("https://mi-linux.wlv.ac.uk/~2214055/silver/UserData.php"))
        {
            yield return www.SendWebRequest();

            if (www.isNetworkError || www.isHttpError)
            {
                Debug.Log(www.error);
            }
            else
            {
                string patientsString = www.downloadHandler.text;
                patients = patientsString.Split(";");
            }
        }

        patientList();
    }

    public void patientList()
    {
        string patientList = "";

        foreach(string patient in patients)
        {
            if (patient == "") break;

            int carerID = Int32.Parse(GetValue(patient, "Carer_ID"));

            if (carerID == ID)
            {
                string name = "first: " + GetValue(patient, "First_Name") + ", last: " + GetValue(patient, "Last_Name");
                names.Add(name);
                patientList += name + "\n";
            }
        }

        content.GetComponent<TextMeshProUGUI>().text = patientList;
    }

    string GetValue(string data, string index)
    {
        string value = data.Substring(data.IndexOf(index) + index.Length + 1);
        if (value.Contains("|")) value = value.Remove(value.IndexOf("|"));

        return value;
    }

    // Update is called once per frame
    void Update()
    {
        
    }
}
