package com.joblens.joblens.entity;

import jakarta.persistence.Entity;
import jakarta.persistence.Id;
import jakarta.persistence.Table;

@Entity
@Table(name = "riskquickscan")
public class RiskQuickScan {

    @Id
    private Integer companyID;

    private String antiCompetitiveBehavior;
    private String cybersecurity;

    // Getters and setters
    public Integer getCompanyID() {
        return companyID;
    }

    public void setCompanyID(Integer companyID) {
        this.companyID = companyID;
    }

    public String getAntiCompetitiveBehavior() {
        return antiCompetitiveBehavior;
    }

    public void setAntiCompetitiveBehavior(String antiCompetitiveBehavior) {
        this.antiCompetitiveBehavior = antiCompetitiveBehavior;
    }

    public String getCybersecurity() {
        return cybersecurity;
    }

    public void setCybersecurity(String cybersecurity) {
        this.cybersecurity = cybersecurity;
    }
}
