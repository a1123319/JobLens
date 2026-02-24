package com.joblens.joblens.entity;

import jakarta.persistence.Entity;
import jakarta.persistence.Id;
import jakarta.persistence.OneToOne;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.Table;

@Entity
@Table(name = "em")
public class Em {

    @Id
    private Integer companyId;

    @OneToOne
    @JoinColumn(name = "CompanyId", referencedColumnName = "Id", insertable = false, updatable = false)
    private Company company;

    private Integer renewEngergyUsageRate;

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

    public Integer getRenewEngergyUsageRate() {
        return renewEngergyUsageRate;
    }

    public void setRenewEngergyUsageRate(Integer renewEngergyUsageRate) {
        this.renewEngergyUsageRate = renewEngergyUsageRate;
    }
}
