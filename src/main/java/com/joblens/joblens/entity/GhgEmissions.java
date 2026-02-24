package com.joblens.joblens.entity;

import jakarta.persistence.Entity;
import jakarta.persistence.Id;
import jakarta.persistence.OneToOne;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.Table;

@Entity
@Table(name = "ghgemissions")
public class GhgEmissions {

    @Id
    private Integer companyId;

    @OneToOne
    @JoinColumn(name = "CompanyId", referencedColumnName = "Id", insertable = false, updatable = false)
    private Company company;

    private Integer scope1EmissionTonCO2e;
    private Integer scope2EmissionTonCO2e;
    private Integer scope3EmissionTonCO2e;
    private Integer ghgEmissionIntensity;

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

    public Integer getScope1EmissionTonCO2e() {
        return scope1EmissionTonCO2e;
    }

    public void setScope1EmissionTonCO2e(Integer scope1EmissionTonCO2e) {
        this.scope1EmissionTonCO2e = scope1EmissionTonCO2e;
    }

    public Integer getScope2EmissionTonCO2e() {
        return scope2EmissionTonCO2e;
    }

    public void setScope2EmissionTonCO2e(Integer scope2EmissionTonCO2e) {
        this.scope2EmissionTonCO2e = scope2EmissionTonCO2e;
    }

    public Integer getScope3EmissionTonCO2e() {
        return scope3EmissionTonCO2e;
    }

    public void setScope3EmissionTonCO2e(Integer scope3EmissionTonCO2e) {
        this.scope3EmissionTonCO2e = scope3EmissionTonCO2e;
    }

    public Integer getGhgEmissionIntensity() {
        return ghgEmissionIntensity;
    }

    public void setGhgEmissionIntensity(Integer ghgEmissionIntensity) {
        this.ghgEmissionIntensity = ghgEmissionIntensity;
    }
}
