using System;
using System.Collections;
using System.Collections.Generic;
using TMPro;
using UnityEngine;
using UnityEngine.Networking;
using UnityEngine.SceneManagement;

public class DataCollectorLogin : MonoBehaviour
{    
    public string[] employees;

    [SerializeField] TMP_InputField idInput;
    [SerializeField] TMP_InputField passwordInput;    

    // Start is called before the first frame update
    void Start()
    {
        //StartCoroutine(GetData());
    }

    public void Login()
    {
        StartCoroutine(sendLogin());
    }

    IEnumerator sendLogin()
    {
        WWWForm form = new WWWForm();
        form.AddField("loginUser", idInput.text);
        form.AddField("loginPass", passwordInput.text);
        bool connected = false;

        using (UnityWebRequest www = UnityWebRequest.Post("https://mi-linux.wlv.ac.uk/~2214055/silver/Login.php", form))
        {
            yield return www.SendWebRequest();

            if (www.isNetworkError || www.isHttpError)
            {
                Debug.Log(www.error);
            }
            else
            {                
                Debug.Log("Form upload completed!");
                connected = true;
            }
        }        

        if (connected)
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
                    string employeesString = www.downloadHandler.text;
                    employees = employeesString.Split(";");
                    userData();
                }
            }
        }
    }

    public void userData()
    {
        bool valid = false;

        foreach (string employee in employees)
        {
            if (employee == "") break;

            string id = GetValue(employee, "ID");            

            if (idInput.text == id)
            {
                Monitor.Name = GetValue(employee, "First_Name") + " " + GetValue(employee, "Last_Name");
                Monitor.Email = GetValue(employee, "Email");
                Monitor.NHS_Number = Int32.Parse(GetValue(employee, "NHS_Number"));
                Monitor.ID = Int32.Parse(id);
                Monitor.Employee = Int32.Parse(GetValue(employee, "Employee"));
                SceneManager.LoadScene("MainLobby");

                valid = true;
                break;
            }
        }

        if (!valid)
        {
            idInput.text = "Invalid!";
            passwordInput.text = "Invalid!";
        }
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
