package com.joblens.joblens.entity;

import jakarta.persistence.Entity;
import jakarta.persistence.Id;
import jakarta.persistence.OneToOne;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.Table;

@Entity
@Table(name = "safetyrisk")
public class SafetyRisk {

    @Id
    private Integer companyId;

    @OneToOne
    @JoinColumn(name = "CompanyId", referencedColumnName = "Id", insertable = false, updatable = false)
    private Company company;

    private Double occupationalInjuryRate;
    private Integer occupationInjuryCount;

    // Getters and setters
    public Integer getCompanyId() {
        return companyId;
    }

    public void setCompanyId(Integer companyId) {
        this.companyId = companyId;
    }

    public Company getCompany() {
        return company;
    }

    public void setCompany(Company company) {
        this.company = company;
    }

    public Double getOccupationalInjuryRate() {
        return occupationalInjuryRate;
    }

    public void setOccupationalInjuryRate(Double occupationalInjuryRate) {
        this.occupationalInjuryRate = occupationalInjuryRate;
    }

    public Integer getOccupationInjuryCount() {
        return occupationInjuryCount;
    }

    public void setOccupationInjuryCount(Integer occupationInjuryCount) {
        this.occupationInjuryCount = occupationInjuryCount;
    }
}
